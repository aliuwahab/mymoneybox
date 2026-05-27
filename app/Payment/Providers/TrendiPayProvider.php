<?php

namespace App\Payment\Providers;

use App\Payment\PaymentProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrendiPayProvider implements PaymentProviderInterface
{
    protected string $apiKey;             // Used as Bearer token for disbursements / API calls

    protected string $merchantExternalId; // Used as Bearer token for checkout

    protected string $checkoutTerminalId; // Used for payment link / checkout flow

    protected string $apiTerminalId;      // Used for disbursements & name enquiry

    protected string $checkoutBaseUrl;

    protected string $apiBaseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('payment.trendipay.api_key', '');
        $this->merchantExternalId = (string) config('payment.trendipay.merchant_external_id', '');
        $this->checkoutTerminalId = (string) config('payment.trendipay.checkout_terminal_id', '');
        $this->apiTerminalId = (string) config('payment.trendipay.api_terminal_id', '');
        $this->checkoutBaseUrl = config('payment.trendipay.checkout_base_url', 'https://test-api.bsl.com.gh');
        $this->apiBaseUrl = config('payment.trendipay.api_base_url', $this->checkoutBaseUrl);
    }

    public function initializePayment(array $data): array
    {
        $amountInMinorUnits = (int) ($data['amount'] * 100);

        $payload = [
            'terminalExternalId' => $this->checkoutTerminalId,
            'amount' => $amountInMinorUnits,
            'description' => $data['description'] ?? 'MyPiggyBox Contribution',
            'reference' => $data['reference'] ?? 'contrib_'.uniqid(),
            'returnUrl' => $data['return_url'],
            'callbackUrl' => $data['webhook_url'] ?? route('trendipay.webhook'),
            'paymentMethods' => ['cards', 'mobile money', 'bank'],
        ];

        if (isset($data['metadata']['money_box_title'])) {
            $payload['items'] = [[
                'name' => $data['metadata']['money_box_title'],
                'price' => (string) $amountInMinorUnits,
            ]];
        }

        $url = "{$this->checkoutBaseUrl}/v1/payment-links";

        Log::info('TrendiPay [payment-link] request', [
            'url' => $url,
            'reference' => $payload['reference'],
            'amount' => $amountInMinorUnits,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->merchantExternalId,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $result = $response->json();

            Log::info('TrendiPay [payment-link] response', [
                'url' => $url,
                'status' => $response->status(),
                'success' => $result['success'] ?? null,
                'reference' => $payload['reference'],
            ]);

            if ($response->successful() && ($result['success'] ?? false) && isset($result['data'])) {
                return [
                    'success' => true,
                    'payment_url' => $result['data'],
                    'reference' => $payload['reference'],
                    'provider' => 'trendipay',
                ];
            }

            Log::warning('TrendiPay [payment-link] failed', [
                'url' => $url,
                'response' => $result,
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Unable to create payment link. Please try again.',
            ];
        } catch (\Exception $e) {
            Log::error('TrendiPay [payment-link] exception', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'Payment service unavailable. Please try again later.'];
        }
    }

    public function verifyPayment(string $reference): array
    {
        $url = "{$this->checkoutBaseUrl}/v1/transactions/{$reference}/status";

        Log::info('TrendiPay [verify-payment] request', ['url' => $url, 'reference' => $reference]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->merchantExternalId,
                'Accept' => 'application/json',
            ])->get($url);

            $result = $response->json();

            Log::info('TrendiPay [verify-payment] response', [
                'url' => $url,
                'status' => $response->status(),
                'reference' => $reference,
                'data' => $result['data'] ?? null,
            ]);

            if ($response->successful() && isset($result['data'])) {
                $data = $result['data'];
                $isSuccessful = strtolower($data['status']) === 'success';

                return [
                    'success' => $isSuccessful,
                    'status' => $isSuccessful ? 'completed' : 'failed',
                    'amount' => ($data['amount'] ?? 0) / 100,
                    'reference' => $data['reference'] ?? $reference,
                    'transaction_rrn' => $data['rrn'] ?? null,
                    'currency' => 'GHS',
                    'paid_at' => now()->toDateTimeString(),
                    'raw_data' => $data,
                ];
            }

            return ['success' => false, 'status' => 'failed', 'message' => $result['message'] ?? 'Payment verification failed'];
        } catch (\Exception $e) {
            Log::error('TrendiPay [verify-payment] exception', ['error' => $e->getMessage(), 'reference' => $reference]);

            return ['success' => false, 'status' => 'failed', 'message' => 'Payment verification failed. Please contact support.'];
        }
    }

    public function handleWebhook(array $payload): array
    {
        if (! isset($payload['data'])) {
            throw new \Exception('Invalid webhook payload: missing data');
        }

        $data = $payload['data'];
        $status = strtolower($data['status'] ?? 'unknown');

        $internalStatus = match ($status) {
            'success' => 'completed',
            'failed', 'cancelled', 'expired' => 'failed',
            default => 'pending',
        };

        Log::info('TrendiPay [webhook] parsed', [
            'reference' => $data['reference'] ?? null,
            'status' => $status,
            'internal_status' => $internalStatus,
            'amount' => ($data['amount'] ?? 0) / 100,
            'rrn' => $data['rrn'] ?? null,
            'response_code' => $data['responseCode'] ?? null,
            'reason' => $data['reason'] ?? null,
            'type' => $data['type'] ?? null,
        ]);

        return [
            'success' => $status === 'success',
            'status' => $internalStatus,
            'payment_status' => $status,
            'amount' => ($data['amount'] ?? 0) / 100,
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
    }

    public function getBalance(): array
    {
        $url = "{$this->apiBaseUrl}/v1/balance";

        Log::info('TrendiPay [balance] request', ['url' => $url]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->get($url);

            $result = $response->json();

            Log::info('TrendiPay [balance] response', [
                'url' => $url,
                'status' => $response->status(),
                'available_pesewas' => $result['data']['availableBalance'] ?? null,
                'actual_pesewas' => $result['data']['actualBalance'] ?? null,
            ]);

            if ($response->successful() && ($result['success'] ?? false) && isset($result['data'])) {
                return [
                    'success' => true,
                    'available_balance' => ($result['data']['availableBalance'] ?? 0) / 100,
                    'actual_balance' => ($result['data']['actualBalance'] ?? 0) / 100,
                    'available_formatted' => $result['data']['availableBalanceInMajorUnitsFormatted'] ?? null,
                ];
            }

            Log::warning('TrendiPay [balance] failed', ['url' => $url, 'response' => $result]);

            return ['success' => false, 'message' => $result['message'] ?? 'Unable to fetch balance.'];
        } catch (\Exception $e) {
            Log::error('TrendiPay [balance] exception', ['error' => $e->getMessage()]);

            return ['success' => false, 'message' => 'Balance check unavailable.'];
        }
    }

    public function verifyAccountName(string $accountNumber, string $rSwitch): array
    {
        $url = "{$this->apiBaseUrl}/v1/terminals/{$this->apiTerminalId}/account-details";

        Log::info('TrendiPay [name-enquiry] request', [
            'url' => $url,
            'accountNumber' => $accountNumber,
            'rSwitch' => $rSwitch,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
            ])->get($url, [
                'rSwitch' => $rSwitch,
                'accountNumber' => $accountNumber,
            ]);

            $result = $response->json();

            Log::info('TrendiPay [name-enquiry] response', [
                'url' => $url,
                'status' => $response->status(),
                'success' => $result['success'] ?? null,
                'accountName' => $result['data']['accountName'] ?? null,
                'accountNumber' => $accountNumber,
            ]);

            if ($response->successful() && ($result['success'] ?? false) && isset($result['data']['accountName'])) {
                return [
                    'success' => true,
                    'account_name' => $result['data']['accountName'],
                    'account_number' => $result['data']['accountNumber'],
                ];
            }

            Log::warning('TrendiPay [name-enquiry] failed', [
                'url' => $url,
                'response' => $result,
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Account verification failed. Please check the number and network.',
            ];
        } catch (\Exception $e) {
            Log::error('TrendiPay [name-enquiry] exception', ['error' => $e->getMessage(), 'accountNumber' => $accountNumber]);

            return ['success' => false, 'message' => 'Account verification unavailable. Please try again.'];
        }
    }

    public function transferAmount(array $data): array
    {
        $amountInMinorUnits = (int) ($data['amount'] * 100);

        $payload = [
            'reference' => $data['reference'],
            'accountNumber' => $data['account_number'],
            'rSwitch' => $data['network'] ?? 'mtn', // plain code: mtn, vodafone, airteltigo
            'description' => $data['description'] ?? 'Withdrawal',
            'amount' => $amountInMinorUnits,
            'accountName' => $data['account_name'] ?? '',
            'senderName' => $data['sender_name'] ?? config('app.name'),
            'callbackUrl' => $data['callback_url'] ?? route('trendipay.webhook'),
        ];

        $url = "{$this->apiBaseUrl}/v1/terminals/{$this->apiTerminalId}/disbursements";

        Log::info('TrendiPay [disburse] request', [
            'url' => $url,
            'reference' => $payload['reference'],
            'accountNumber' => $payload['accountNumber'],
            'rSwitch' => $payload['rSwitch'],
            'amount_pesewas' => $amountInMinorUnits,
            'accountName' => $payload['accountName'],
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $result = $response->json();

            Log::info('TrendiPay [disburse] response', [
                'url' => $url,
                'http_status' => $response->status(),
                'success' => $result['success'] ?? null,
                'reference' => $payload['reference'],
                'external_id' => $result['data']['externalId'] ?? null,
                'internal_id' => $result['data']['internalId'] ?? null,
                'status' => $result['data']['status'] ?? null,
                'response_code' => $result['data']['responseCode'] ?? null,
                'reason' => $result['data']['reason'] ?? null,
            ]);

            if ($response->successful() && ($result['success'] ?? false)) {
                return [
                    'success' => true,
                    'transaction_reference' => $result['data']['externalId'] ?? $result['data']['reference'] ?? $data['reference'],
                    'status' => 'processing',
                    'provider' => 'trendipay',
                    'raw_data' => $result,
                ];
            }

            Log::warning('TrendiPay [disburse] failed', [
                'url' => $url,
                'response' => $result,
                'curl' => "curl --location '{$url}' --header 'Authorization: Bearer {$this->apiKey}' --header 'Accept: application/json' --header 'Content-Type: application/json' --data '".json_encode($payload)."'",
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Disbursement failed. Please try again.',
                'error_code' => $result['code'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('TrendiPay [disburse] exception', [
                'url' => $url,
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return ['success' => false, 'message' => 'Disbursement service unavailable. Please try again later.'];
        }
    }

    public function getName(): string
    {
        return 'trendipay';
    }
}
