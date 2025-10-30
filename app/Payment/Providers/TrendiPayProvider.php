<?php

namespace App\Payment\Providers;

use App\Payment\PaymentProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrendiPayProvider implements PaymentProviderInterface
{
    protected string $apiKey;
    protected string $terminalExternalId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('payment.trendipay.api_key');
        $this->terminalExternalId = config('payment.trendipay.terminal_external_id');
        $this->baseUrl = config('payment.trendipay.base_url', 'https://test-api.bsl.com.gh');
    }

    public function initializePayment(array $data): array
    {
        try {
            // Amount should be in minor units (pesewas for GHS)
            $amountInMinorUnits = (int) ($data['amount'] * 100);

            $payload = [
                'terminalExternalId' => $this->terminalExternalId,
                'amount' => $amountInMinorUnits,
                'description' => $data['description'] ?? 'Piggy Box Contribution',
                'reference' => $data['reference'] ?? 'trendipay_' . uniqid(),
                'returnUrl' => $data['callback_url'] ?? route('contributions.callback'),
                'callbackUrl' => route('webhooks.trendipay'),
                'paymentMethods' => ['cards', 'mobile money', 'bank'],
            ];

            // Add metadata as items if provided
            if (isset($data['metadata'])) {
                $payload['items'] = [[
                    'name' => $data['metadata']['money_box_title'] ?? 'Contribution',
                    'price' => (string) $amountInMinorUnits,
                    'currency' => 'GHS'
                ]];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/v1/payment-links", $payload);

            $result = $response->json();

            dd($result);

            Log::info('TrendiPay initialization response', [
                'status' => $response->status(),
                'response' => $result
            ]);

            if ($response->successful() && isset($result['data']['paymentLink'])) {
                return [
                    'success' => true,
                    'payment_url' => $result['data']['paymentLink'],
                    'reference' => $payload['reference'],
                    'transaction_rrn' => $result['data']['transactionRRN'] ?? null,
                    'provider' => 'trendipay',
                ];
            }

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Payment initialization failed',
            ];
        } catch (\Exception $e) {
            Log::error('TrendiPay initialization error', [
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
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("{$this->baseUrl}/v1/transactions/{$reference}/status");

            $result = $response->json();

            Log::info('TrendiPay verification response', [
                'status' => $response->status(),
                'response' => $result
            ]);

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
            Log::info('TrendiPay webhook received', [
                'payload' => $payload,
                'headers' => request()->headers->all()
            ]);

            // Extract data from webhook
            if (!isset($payload['data'])) {
                throw new \Exception('Invalid webhook payload: missing data');
            }

            $data = $payload['data'];
            $isSuccessful = strtolower($data['status']) === 'success';

            return [
                'success' => $isSuccessful,
                'status' => $isSuccessful ? 'completed' : 'failed',
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
