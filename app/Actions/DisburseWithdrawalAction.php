<?php

namespace App\Actions;

use App\Enums\WithdrawalStatus;
use App\Models\MoneyBoxWithdrawal;
use App\Models\PiggyBoxWithdrawal;
use App\Payment\PaymentManager;
use App\Payment\Providers\TrendiPayProvider;
use Illuminate\Support\Facades\Log;

class DisburseWithdrawalAction
{
    /**
     * Submit the withdrawal to the payment provider.
     * Status is set to Processing; it moves to Disbursed only after the webhook confirms.
     *
     * @return array{success: bool, message?: string}
     */
    public function execute(MoneyBoxWithdrawal|PiggyBoxWithdrawal $withdrawal): array
    {
        $account = $withdrawal->withdrawalAccount;

        if (!$account) {
            Log::error('DisburseWithdrawalAction: no withdrawal account', ['reference' => $withdrawal->reference]);
            return ['success' => false, 'message' => 'No withdrawal account found.'];
        }

        if (!$account->is_verified) {
            Log::error('DisburseWithdrawalAction: account not verified', [
                'reference'  => $withdrawal->reference,
                'account_id' => $account->id,
            ]);
            return ['success' => false, 'message' => 'Withdrawal account is not verified. Cannot disburse.'];
        }

        try {
            $provider = app(PaymentManager::class)->provider($withdrawal->payment_provider ?? 'trendipay');

            // Check TrendiPay balance before attempting disbursement
            if ($provider instanceof TrendiPayProvider) {
                $balance = $provider->getBalance();

                if (!$balance['success']) {
                    Log::warning('DisburseWithdrawalAction: balance check failed', [
                        'reference' => $withdrawal->reference,
                        'message'   => $balance['message'] ?? null,
                    ]);
                    return ['success' => false, 'message' => 'Could not verify available balance. Please try again.'];
                }

                $availableBalance = $balance['available_balance'];

                Log::info('DisburseWithdrawalAction: balance check', [
                    'reference'         => $withdrawal->reference,
                    'available_balance' => $availableBalance,
                    'net_amount'        => $withdrawal->net_amount,
                ]);

                if ($availableBalance < $withdrawal->net_amount) {
                    Log::error('DisburseWithdrawalAction: insufficient balance', [
                        'reference'         => $withdrawal->reference,
                        'available_balance' => $availableBalance,
                        'net_amount'        => $withdrawal->net_amount,
                    ]);
                    return [
                        'success' => false,
                        'message' => "Insufficient TrendiPay balance. Available: GHS {$balance['available_formatted']}. Required: GHS {$withdrawal->net_amount}.",
                    ];
                }
            }

            $transferData = [
                'reference'      => $withdrawal->reference . '-DISBURSE-' . now()->timestamp,
                'amount'         => $withdrawal->net_amount,
                'account_number' => $account->account_number,
                'account_name'   => $account->account_name,
                'network'        => $account->mobile_network?->value ?? 'mtn', // plain code: mtn, vodafone, airteltigo
                'sender_name'    => config('app.name'),
                'description'    => "Withdrawal: {$withdrawal->reference}",
            ];

            $result = $provider->transferAmount($transferData);

            if ($result['success']) {
                $withdrawal->update([
                    'status'                => WithdrawalStatus::Processing,
                    'transaction_reference' => $result['transaction_reference'] ?? null,
                    'payment_metadata'      => array_merge($withdrawal->payment_metadata ?? [], [
                        'disbursement'              => $result,
                        'disbursement_submitted_at' => now()->toDateTimeString(),
                    ]),
                ]);

                Log::info('DisburseWithdrawalAction: transfer submitted', ['reference' => $withdrawal->reference]);
                return ['success' => true];
            }

            $withdrawal->update([
                'payment_metadata' => array_merge($withdrawal->payment_metadata ?? [], [
                    'disbursement_attempt' => $result,
                    'attempted_at'         => now()->toDateTimeString(),
                ]),
            ]);

            Log::warning('DisburseWithdrawalAction: transfer failed', [
                'reference' => $withdrawal->reference,
                'message'   => $result['message'] ?? null,
            ]);

            return ['success' => false, 'message' => $result['message'] ?? 'Transfer failed. Please try again.'];

        } catch (\Exception $e) {
            Log::error('DisburseWithdrawalAction: exception', [
                'reference' => $withdrawal->reference,
                'error'     => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'Disbursement service unavailable. Please try again.'];
        }
    }
}