<?php

namespace App\Actions;

use App\Enums\WithdrawalStatus;
use App\Models\MoneyBoxWithdrawal;
use App\Models\PiggyBoxWithdrawal;
use App\Payment\PaymentManager;
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

        try {
            $provider = app(PaymentManager::class)->provider($withdrawal->payment_provider ?? 'trendipay');

            $transferData = [
                'reference'    => $withdrawal->reference . '-DISBURSE-' . now()->timestamp,
                'amount'       => $withdrawal->net_amount,
                'account_number' => $account->account_number,
                'account_name'   => $account->account_name,
                'account_type'   => $account->account_type->value,
                'network'        => $account->mobile_network?->trendiPayShortCode() ?? 'mtngh',
                'sender_name'    => config('app.name'),
                'description'    => "Withdrawal: {$withdrawal->reference}",
            ];

            $result = $provider->transferAmount($transferData);

            if ($result['success']) {
                $withdrawal->update([
                    'status'                => WithdrawalStatus::Processing,
                    'transaction_reference' => $result['transaction_reference'] ?? null,
                    'payment_metadata'      => array_merge($withdrawal->payment_metadata ?? [], [
                        'disbursement'             => $result,
                        'disbursement_submitted_at' => now()->toDateTimeString(),
                    ]),
                ]);

                Log::info('DisburseWithdrawalAction: transfer submitted', ['reference' => $withdrawal->reference]);
                return ['success' => true];
            }

            // API returned failure — keep status as Approved so the admin can retry
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
