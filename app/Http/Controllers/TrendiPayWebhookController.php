<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrendiPayWebhookController extends Controller
{
    public function __construct(
        protected PaymentManager $paymentManager
    ) {}

    /**
     * Handle TrendiPay webhook callback
     */
    public function handle(Request $request)
    {
        try {
            Log::info('TrendiPay webhook received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Process the webhook through the provider
            $webhookData = $this->paymentManager->provider('trendipay')->handleWebhook($request->all());

            // Find the contribution by reference
            $contribution = Contribution::where('payment_reference', $webhookData['reference'])->first();

            if (!$contribution) {
                Log::warning('TrendiPay webhook: Contribution not found', [
                    'reference' => $webhookData['reference']
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Contribution not found'
                ], 404);
            }

            // Update contribution if status is success
            if ($webhookData['success']) {
                $contribution->update([
                    'payment_status' => \App\Enums\PaymentStatus::Completed,
                ]);

                // Update piggy box stats
                $moneyBox = $contribution->moneyBox;
                $moneyBox->increment('total_contributions', $contribution->amount);
                $moneyBox->increment('contribution_count');

                Log::info('TrendiPay webhook: Payment completed', [
                    'contribution_id' => $contribution->id,
                    'amount' => $contribution->amount
                ]);
            } else {
                $contribution->update([
                    'payment_status' => \App\Enums\PaymentStatus::Failed,
                ]);

                Log::warning('TrendiPay webhook: Payment failed', [
                    'contribution_id' => $contribution->id,
                    'reason' => $webhookData['reason'] ?? 'Unknown'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook processed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('TrendiPay webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
