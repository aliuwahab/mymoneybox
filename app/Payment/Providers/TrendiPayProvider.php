<?php

namespace App\Payment\Providers;

use App\Payment\PaymentProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrendiPayProvider implements PaymentProviderInterface
{
    protected string $merchantExternalId; // Used as Bearer token
    protected string $checkoutTerminalId; // Used in payload
    protected string $checkoutBaseUrl;

    public function __construct()
    {
        // According to docs: Use Merchant External ID as Bearer token, NOT the API key
        $this->merchantExternalId = config('payment.trendipay.merchant_external_id');
        $this->checkoutTerminalId = config('payment.trendipay.checkout_terminal_id');
        $this->checkoutBaseUrl = config('payment.trendipay.base_url', 'https://test-api.bsl.com.gh');
    }

    public function initializePayment(array $data): array
    {
        try {
            // Convert amount to minor units (pesewas for GHS)
            // Example: GHS 10.50 becomes 1050 pesewas
            $amountInMinorUnits = (int) ($data['amount'] * 100);

            // Build the payload according to Trendipay Checkout API specs
            $payload = [
                // REQUIRED: Your terminal/merchant identifier
                'terminalExternalId' => $this->checkoutTerminalId,

                // REQUIRED: Amount in minor units (pesewas)
                'amount' => $amountInMinorUnits,

                // REQUIRED: Transaction description (shown to customer)
                'description' => $data['description'] ?? 'MyPiggyBox Contribution',

                // REQUIRED: Your unique transaction reference (for tracking)
                'reference' => $data['reference'] ?? 'contrib_' . uniqid(),

                // REQUIRED: URL to redirect customer after payment (success or fail)
                'returnUrl' => $data['return_url'],

                // REQUIRED: Webhook URL for server-to-server notifications
                'callbackUrl' => $data['webhook_url'] ?? route('trendipay.webhook'),

                // OPTIONAL: Limit available payment methods
                'paymentMethods' => ['cards', 'mobile money', 'bank'],
            ];

            // OPTIONAL: Add itemized breakdown (helps with accounting/reconciliation)
            // Note: TrendiPay only accepts 'name' and 'price' in items array
            if (isset($data['metadata']['money_box_title'])) {
                $payload['items'] = [[
                    'name' => $data['metadata']['money_box_title'],
                    'price' => (string) $amountInMinorUnits,
                ]];
            }

            $url = "{$this->checkoutBaseUrl}/v1/payment-links";

            // Send request to Trendipay Checkout API (using required headers from docs)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->merchantExternalId,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $result = $response->json();

            Log::warning("TrendiPay Payment Generation Response", ["response" => $result, 'requestUrl' => $url]);

            // Check if payment link was created successfully
            // TrendiPay returns the URL directly in 'data' as a string, not nested
            if ($response->successful() && isset($result['success']) && $result['success'] === true && isset($result['data'])) {
                return [
                    'success' => true,
                    'payment_url' => $result['data'], // URL is returned directly as string
                    'reference' => $payload['reference'], // Our reference for tracking
                    'provider' => 'trendipay',
                ];
            }

            Log::warning("TrendiPay Payment Link Error", ["response" => $result, 'requestUrl' => $url]);

            // Payment link creation failed
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Unable to create payment link. Please try again.',
            ];
        } catch (\Exception $e) {
            Log::error('TrendiPay: Payment initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'message' => 'Payment service unavailable. Please try again later.',
            ];
        }
    }

    public function verifyPayment(string $reference): array
    {
        try {
            // The reference is actually the transaction RRN
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->merchantExternalId,
                'Accept' => 'application/json',
            ])->get("{$this->checkoutBaseUrl}/v1/transactions/{$reference}/status");

            $result = $response->json();

            if ($response->successful() && isset($result['data'])) {
                $data = $result['data'];
                $isSuccessful = strtolower($data['status']) === 'success';

                return [
                    'success' => $isSuccessful,
                    'status' => $isSuccessful ? 'completed' : 'failed',
                    'amount' => ($data['amount'] ?? 0) / 100, // Convert from minor units
                    'reference' => $data['reference'] ?? $reference,
                    'transaction_rrn' => $data['rrn'] ?? null,
                    'currency' => 'GHS',
                    'paid_at' => now()->toDateTimeString(),
                    'raw_data' => $data,
                ];
            }

            return [
                'success' => false,
                'status' => 'failed',
                'message' => $result['message'] ?? 'Payment verification failed',
            ];
        } catch (\Exception $e) {
            Log::error('TrendiPay verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'reference' => $reference
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Payment verification failed. Please contact support.',
            ];
        }
    }

    public function handleWebhook(array $payload): array
    {
        try {
            // Extract data from webhook
            if (!isset($payload['data'])) {
                throw new \Exception('Invalid webhook payload: missing data');
            }

            $data = $payload['data'];
            $status = strtolower($data['status'] ?? 'unknown');

            // Map TrendiPay status to our internal status
            // TrendiPay statuses: success, pending, failed, cancelled, expired
            $isSuccessful = $status === 'success';
            $internalStatus = match($status) {
                'success' => 'completed',
                'failed', 'cancelled', 'expired' => 'failed',
                default => 'pending'
            };

            return [
                'success' => $isSuccessful,
                'status' => $internalStatus,
                'payment_status' => $status, // Original TrendiPay status
                'amount' => ($data['amount'] ?? 0) / 100, // Convert from minor units
                'reference' => $data['reference'] ?? null,
                'transaction_rrn' => $data['rrn'] ?? null,
                'transaction_id' => $data['internalId'] ?? null,
                'external_id' => $data['externalId'] ?? null,
                'account_number' => $data['accountNumber'] ?? null,
                'payment_method' => $data['rSwitch'] ?? null,
                'response_code' => $data['responseCode'] ?? null,
                'reason' => $data['reason'] ?? null,
                'currency' => 'GHS',
                'paid_at' => now()->toDateTimeString(),
                'raw_data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('TrendiPay webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $payload
            ]);

            throw $e;
        }
    }

    public function getName(): string
    {
        return 'trendipay';
    }
}
