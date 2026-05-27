<?php

namespace App\Http\Controllers;

use App\Actions\CompletePiggyDonationAction;
use App\Enums\PaymentStatus;
use App\Models\PiggyDonation;
use App\Payment\Providers\TrendiPayProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PiggyWebhookController extends Controller
{
    public function __construct(
        protected TrendiPayProvider $trendiPayProvider,
        protected CompletePiggyDonationAction $completeDonation,
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
            'headers' => $request->headers->all(),
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

            if (! $donation) {
                Log::warning('TrendiPay Piggy Webhook: Donation not found', ['reference' => $reference]);

                return response()->json(['status' => 'error', 'message' => 'Donation not found'], 404);
            }

            // Idempotency: terminal states should not be reprocessed
            if (in_array($donation->payment_status, [PaymentStatus::Completed, PaymentStatus::Failed])) {
                Log::info('TrendiPay Piggy Webhook: already processed, skipping', [
                    'reference' => $reference,
                    'current_status' => $donation->payment_status->value,
                ]);

                return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
            }

            $paymentStatus = match ($webhookData['status']) {
                'completed' => PaymentStatus::Completed,
                'failed' => PaymentStatus::Failed,
                default => PaymentStatus::Pending,
            };

            if (in_array($paymentStatus, [PaymentStatus::Completed, PaymentStatus::Failed], true)) {
                $webhookData = $this->withVerifiedCollectionTransaction($webhookData);

                if (($webhookData['provider_verified'] ?? true) === false) {
                    $donation->forceFill([
                        'transaction_rrn' => $webhookData['transaction_rrn'] ?? $donation->transaction_rrn,
                        'payment_metadata' => array_merge($donation->payment_metadata ?? [], [
                            'webhook' => $webhookData['raw_data'] ?? [],
                        ]),
                    ])->save();

                    return response()->json(['status' => 'pending', 'message' => 'Payment verification pending'], 202);
                }

                $paymentStatus = match ($webhookData['status']) {
                    'completed' => PaymentStatus::Completed,
                    'failed' => PaymentStatus::Failed,
                    default => PaymentStatus::Pending,
                };
            }

            if ($paymentStatus === PaymentStatus::Completed) {
                $donation = $this->completeDonation->execute($donation, $webhookData, 'webhook');
                $paymentStatus = $donation->payment_status;
            } else {
                $donation->forceFill([
                    'payment_status' => $paymentStatus,
                    'transaction_rrn' => $webhookData['transaction_rrn'] ?? null,
                    'payment_metadata' => array_merge($donation->payment_metadata ?? [], [
                        'webhook' => [
                            'at' => now()->toDateTimeString(),
                            'status' => $webhookData['status'],
                            'raw_data' => $webhookData['raw_data'] ?? null,
                        ],
                    ]),
                ])->save();
            }

            Log::info('TrendiPay Piggy Webhook: status updated', [
                'donation_id' => $donation->id,
                'reference' => $reference,
                'status' => $paymentStatus->value,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Piggy payment processed successfully'], 200);

        } catch (\Exception $e) {
            Log::error('TrendiPay Piggy Webhook: Processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal error processing webhook',
            ], 500);
        }
    }

    private function withVerifiedCollectionTransaction(array $webhookData): array
    {
        $verification = $this->trendiPayProvider->verifyCollectionTransaction(
            (string) ($webhookData['transaction_rrn'] ?? ''),
            $webhookData['reference'] ?? null,
        );

        $rawData = array_merge($webhookData['raw_data'] ?? [], [
            'provider_verification' => $verification['raw_data'] ?? $verification,
            'provider_verified' => $verification['verified'] ?? false,
            'provider_verified_at' => now()->toDateTimeString(),
        ]);

        if (($verification['verified'] ?? false) !== true) {
            Log::warning('TrendiPay Piggy Webhook: provider verification failed', [
                'reference' => $webhookData['reference'] ?? null,
                'rrn' => $webhookData['transaction_rrn'] ?? null,
                'message' => $verification['message'] ?? null,
            ]);

            return array_merge($webhookData, [
                'success' => false,
                'status' => 'pending',
                'provider_verified' => false,
                'provider_verification' => $verification,
                'raw_data' => $rawData,
            ]);
        }

        return array_merge($webhookData, [
            'success' => $verification['success'] ?? false,
            'status' => $verification['status'] ?? 'pending',
            'amount' => $verification['amount'] ?? $webhookData['amount'] ?? 0,
            'reference' => $verification['reference'] ?? $webhookData['reference'] ?? null,
            'transaction_rrn' => $verification['transaction_rrn'] ?? $webhookData['transaction_rrn'] ?? null,
            'transaction_id' => $verification['transaction_id'] ?? $webhookData['transaction_id'] ?? null,
            'external_id' => $verification['external_id'] ?? $webhookData['external_id'] ?? null,
            'account_number' => $verification['account_number'] ?? $webhookData['account_number'] ?? null,
            'payment_method' => $verification['payment_method'] ?? $webhookData['payment_method'] ?? null,
            'response_code' => $verification['response_code'] ?? $webhookData['response_code'] ?? null,
            'reason' => $verification['reason'] ?? $webhookData['reason'] ?? null,
            'provider_verified' => true,
            'provider_verification' => $verification,
            'raw_data' => $rawData,
        ]);
    }

    /**
     * Validate webhook payload structure
     *
     * @return JsonResponse|null Returns error response if invalid, null if valid
     */
    protected function validateWebhookPayload(array $payload): ?JsonResponse
    {
        if (! isset($payload['data'])) {
            Log::warning('TrendiPay Piggy Webhook: Invalid payload - missing data field', [
                'payload' => $payload,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing data field',
            ], 400);
        }

        $data = $payload['data'];

        // Check for required fields
        if (! isset($data['reference'])) {
            Log::warning('TrendiPay Piggy Webhook: Invalid payload - missing reference', [
                'payload' => $payload,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing reference',
            ], 400);
        }

        if (! isset($data['status'])) {
            Log::warning('TrendiPay Piggy Webhook: Invalid payload - missing status', [
                'payload' => $payload,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing status',
            ], 400);
        }

        return null; // Validation passed
    }
}
