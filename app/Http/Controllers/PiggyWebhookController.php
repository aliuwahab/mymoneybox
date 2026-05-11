<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\PiggyDonation;
use App\Payment\Providers\TrendiPayProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PiggyWebhookController extends Controller
{
    public function __construct(
        protected TrendiPayProvider $trendiPayProvider
    ) {}

    /**
     * Handle TrendiPay webhook notification for piggy donations (server-to-server)
     *
     * This receives payment status updates from TrendiPay after piggy donation payment
     */
    public function handle(Request $request)
    {
        Log::info('TrendiPay Piggy Webhook received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        $payload = $request->all();

        // Validate webhook payload structure
        $validationFailedResponse = $this->validateWebhookPayload($payload);
        if ($validationFailedResponse) {
            return $validationFailedResponse;
        }

        try {
            // Process the webhook payload through TrendiPay provider
            $webhookData = $this->trendiPayProvider->handleWebhook($payload);
            $reference = $webhookData['reference'];

            // Find the piggy donation
            $donation = PiggyDonation::query()->where('payment_reference', $reference)->first();

            if (!$donation) {
                Log::warning('TrendiPay Piggy Webhook: Donation not found', ['reference' => $reference]);
                return response()->json(['status' => 'error', 'message' => 'Donation not found'], 404);
            }

            // Idempotency: terminal states should not be reprocessed
            if (in_array($donation->payment_status, [PaymentStatus::Completed, PaymentStatus::Failed])) {
                Log::info('TrendiPay Piggy Webhook: already processed, skipping', [
                    'reference'      => $reference,
                    'current_status' => $donation->payment_status->value,
                ]);
                return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
            }

            $paymentStatus = match($webhookData['status']) {
                'completed' => PaymentStatus::Completed,
                'failed'    => PaymentStatus::Failed,
                default     => PaymentStatus::Pending,
            };

            $donation->update([
                'payment_status'   => $paymentStatus,
                'transaction_rrn'  => $webhookData['transaction_rrn'] ?? null,
                'payment_metadata' => $webhookData['raw_data'] ?? null,
            ]);

            Log::info('TrendiPay Piggy Webhook: status updated', [
                'donation_id' => $donation->id,
                'reference'   => $reference,
                'status'      => $paymentStatus->value,
            ]);

            if ($paymentStatus === PaymentStatus::Completed) {
                $piggyBox = $donation->piggyBox;
                $piggyBox->increment('total_received', $donation->amount);
                $piggyBox->increment('donation_count');
            }

            return response()->json(['status' => 'success', 'message' => 'Piggy payment processed successfully'], 200);

        } catch (\Exception $e) {
            Log::error('TrendiPay Piggy Webhook: Processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal error processing webhook'
            ], 500);
        }
    }


    /**
     * Validate webhook payload structure
     *
     * @return JsonResponse|null Returns error response if invalid, null if valid
     */
    protected function validateWebhookPayload(array $payload): ?JsonResponse
    {
        if (!isset($payload['data'])) {
            Log::warning('TrendiPay Piggy Webhook: Invalid payload - missing data field', [
                'payload' => $payload
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing data field'
            ], 400);
        }

        $data = $payload['data'];

        // Check for required fields
        if (!isset($data['reference'])) {
            Log::warning('TrendiPay Piggy Webhook: Invalid payload - missing reference', [
                'payload' => $payload
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing reference'
            ], 400);
        }

        if (!isset($data['status'])) {
            Log::warning('TrendiPay Piggy Webhook: Invalid payload - missing status', [
                'payload' => $payload
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing status'
            ], 400);
        }

        return null; // Validation passed
    }
}
