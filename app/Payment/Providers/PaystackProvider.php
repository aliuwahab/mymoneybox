<?php

namespace App\Payment\Providers;

use App\Payment\PaymentProviderInterface;
use Illuminate\Support\Facades\Http;

class PaystackProvider implements PaymentProviderInterface
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('payment.paystack.secret_key');
        $this->publicKey = config('payment.paystack.public_key');
    }

    public function initializePayment(array $data): array
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email' => $data['email'],
                'amount' => $data['amount'] * 100, // Paystack uses kobo
                'currency' => $data['currency'] ?? 'NGN',
                'reference' => $data['reference'] ?? 'paystack_' . uniqid(),
                'callback_url' => $data['callback_url'] ?? route('payment.callback'),
                'metadata' => $data['metadata'] ?? [],
            ]);

        $result = $response->json();

        if ($result['status']) {
            return [
                'success' => true,
                'payment_url' => $result['data']['authorization_url'],
                'reference' => $result['data']['reference'],
                'access_code' => $result['data']['access_code'],
                'provider' => 'paystack',
            ];
        }

        return [
            'success' => false,
            'message' => $result['message'] ?? 'Payment initialization failed',
        ];
    }

    public function verifyPayment(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

        $result = $response->json();

        if ($result['status'] && $result['data']['status'] === 'success') {
            return [
                'success' => true,
                'status' => 'completed',
                'amount' => $result['data']['amount'] / 100, // Convert from kobo
                'reference' => $result['data']['reference'],
                'currency' => $result['data']['currency'],
                'paid_at' => $result['data']['paid_at'],
            ];
        }

        return [
            'success' => false,
            'status' => $result['data']['status'] ?? 'failed',
            'message' => $result['message'] ?? 'Payment verification failed',
        ];
    }

    public function handleWebhook(array $payload): void
    {
        // Verify webhook signature
        $signature = request()->header('x-paystack-signature');
        $computedSignature = hash_hmac('sha512', json_encode($payload), $this->secretKey);

        if ($signature !== $computedSignature) {
            throw new \Exception('Invalid webhook signature');
        }

        // Process the webhook event based on event type
        // Event types: charge.success, transfer.success, etc.
    }

    public function getName(): string
    {
        return 'paystack';
    }
}
