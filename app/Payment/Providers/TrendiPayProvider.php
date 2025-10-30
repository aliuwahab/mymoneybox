<?php

namespace App\Payment\Providers;

use App\Payment\PaymentProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrendiPayProvider implements PaymentProviderInterface
{
    protected string $apiKey;
    protected string $merchantId;
    protected string $baseUrl = 'https://test-api.bsl.com.gh';

    public function __construct()
    {
        $this->apiKey = config('payment.trendipay.api_key');
        $this->merchantId = config('payment.trendipay.merchant_id');
    }

    public function initializePayment(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/checkout", [
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'GHS',
                'email' => $data['email'],
                'reference' => $data['reference'] ?? 'trendipay_' . uniqid(),
                'callback_url' => $data['callback_url'] ?? route('contributions.callback'),
                'metadata' => $data['metadata'] ?? [],
            ]);

            $result = $response->json();

            dd($result);

            if ($response->successful() && isset($result['data']['checkout_url'])) {
                return [
                    'success' => true,
                    'payment_url' => $result['data']['checkout_url'],
                    'reference' => $result['data']['reference'],
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
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/transaction/verify/{$reference}");

            $result = $response->json();

            if ($response->successful() && $result['data']['status'] === 'successful') {
                return [
                    'success' => true,
                    'status' => 'completed',
                    'amount' => $result['data']['amount'],
                    'reference' => $result['data']['reference'],
                    'currency' => $result['data']['currency'],
                    'paid_at' => $result['data']['paid_at'] ?? now()->toDateTimeString(),
                ];
            }

            return [
                'success' => false,
                'status' => $result['data']['status'] ?? 'failed',
                'message' => $result['message'] ?? 'Payment verification failed',
            ];
        } catch (\Exception $e) {
            Log::error('TrendiPay verification error', [
                'error' => $e->getMessage(),
                'reference' => $reference
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Payment verification failed. Please contact support.',
            ];
        }
    }

    public function handleWebhook(array $payload): void
    {
        try {
            // Verify webhook signature if TrendiPay provides one
            $signature = request()->header('X-TrendiPay-Signature');

            if ($signature) {
                $computedSignature = hash_hmac('sha256', json_encode($payload), $this->apiKey);

                if ($signature !== $computedSignature) {
                    throw new \Exception('Invalid webhook signature');
                }
            }

            // Log webhook for debugging
            Log::info('TrendiPay webhook received', $payload);

            // Process webhook events
            // Event types might include: payment.success, payment.failed, etc.
        } catch (\Exception $e) {
            Log::error('TrendiPay webhook error', [
                'error' => $e->getMessage(),
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
