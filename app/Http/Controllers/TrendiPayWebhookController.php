<?php

namespace App\Http\Controllers;

use App\Actions\UpdateMoneyBoxStatsAction;
use App\Enums\PaymentStatus;
use App\Models\Contribution;
use App\Payment\Providers\TrendiPayProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrendiPayWebhookController extends Controller
{
    public function __construct(
        protected TrendiPayProvider $trendiPayProvider,
        protected UpdateMoneyBoxStatsAction $updateStatsAction
    ) {}

    /**
     * Handle TrendiPay webhook notification (server-to-server)
     *
     * This receives payment status updates from TrendiPay after payment
     */
    public function handle(Request $request)
    {
        try {
            Log::info('TrendiPay webhook received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Process the webhook payload through TrendiPay provider
            $webhookData = $this->trendiPayProvider->handleWebhook($request->all());
            $reference = $webhookData['reference'];

            // Find the contribution
            $contribution = Contribution::query()->where('payment_reference', $reference)->first();

            if (! $contribution) {
                Log::error('TrendiPay webhook: Contribution not found', ['reference' => $reference, 'webhook_data' => $webhookData]);

                return response()->json(['status' => 'error', 'message' => 'Contribution not found'], 404);
            }

            // Map TrendiPay status to our local PaymentStatus enum
            $paymentStatus = match($webhookData['status']) {
                'completed' => PaymentStatus::Completed,
                'failed' => PaymentStatus::Failed,
                default => PaymentStatus::Pending,
            };

            // Update contribution with new status and metadata
            $contribution->update([
                'payment_status' => $paymentStatus,
                'transaction_rrn' => $webhookData['transaction_rrn'] ?? null,
                'payment_metadata' => $webhookData['raw_data'] ?? null,
            ]);

            Log::info('TrendiPay webhook: Status updated', [
                'contribution_id' => $contribution->id,
                'reference' => $reference,
                'status' => $paymentStatus->value,
                'reason' => $webhookData['reason'] ?? null,
            ]);

            // Take action based on status
            if ($paymentStatus === PaymentStatus::Completed) {
                // Update piggy box statistics
                $this->updateStatsAction->execute($contribution->moneyBox, $contribution);

                // TODO: Fire event for payment confirmed notifications
                // event(new PaymentConfirmed($contribution, $contribution->moneyBox));
            }


            // Take action based on status
            if ($paymentStatus === PaymentStatus::Failed) {
                // TODO: Fire event for payment Failed notifications
                // event(new PaymentFailed($contribution, $contribution->moneyBox));
            }

            return response()->json(['status' => 'success', 'message' => 'Payment processed successfully'], 200);

        } catch (Exception $e) {
            Log::error('TrendiPay webhook: Processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json(['status' => 'error', 'message' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle return URL callback (when user is redirected back after payment)
     *
     * This is the user-facing redirect after they complete/cancel payment
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        Log::info('TrendiPay callback: User returned', [
            'reference' => $reference,
            'query_params' => $request->query()
        ]);

        if (!$reference) {
            return redirect()->route('home')
                ->with('error', 'Invalid payment reference.');
        }

        // Find the contribution
        $contribution = Contribution::query()->where('payment_reference', $reference)->first();

        if (!$contribution) {
            Log::error('TrendiPay callback: Contribution not found', [
                'reference' => $reference
            ]);

            return redirect()->route('home')
                ->with('error', 'Contribution not found.');
        }

        // Check if already completed (webhook might have already processed it)
        if ($contribution->payment_status === PaymentStatus::Completed) {
            Log::info('TrendiPay callback: Already completed by webhook', [
                'reference' => $reference,
                'contribution_id' => $contribution->id
            ]);

            return redirect()->route('box.show', $contribution->moneyBox->slug)
                ->with('success', 'Thank you for your contribution!');
        }

        // Verify payment status with TrendiPay API
        $verification = $this->trendiPayProvider->verifyPayment($reference);

        Log::info('TrendiPay callback: Verification result', [
            'reference' => $reference,
            'verification' => $verification
        ]);

        if (!$verification['success']) {
            return redirect()->route('box.show', $contribution->moneyBox->slug)
                ->with('error', 'Payment verification failed. Please contact support if you were charged.');
        }

        // Update contribution if verification is successful
        $contribution->update([
            'payment_status' => PaymentStatus::Completed,
            'transaction_rrn' => $verification['transaction_rrn'] ?? null,
            'payment_metadata' => $verification['raw_data'] ?? null,
        ]);

        // Update piggy box stats
        $this->updateStatsAction->execute($contribution->moneyBox, $contribution);

        return redirect()->route('box.show', $contribution->moneyBox->slug)
            ->with('success', 'Thank you for your contribution!');
    }
}
