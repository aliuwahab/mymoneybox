<?php

namespace App\Actions;

use App\Data\WithdrawalRequestData;
use App\Events\WithdrawalRequested;
use App\Models\MoneyBox;
use App\Models\MoneyBoxWithdrawal;
use App\Models\WithdrawalAccount;
use Illuminate\Support\Facades\DB;

class CreateMoneyBoxWithdrawalAction
{
    public function __construct(
        protected CalculateWithdrawalFeeAction $calculateFee,
    ) {}

    public function execute(MoneyBox $moneyBox, WithdrawalRequestData $data): MoneyBoxWithdrawal
    {
        return DB::transaction(function () use ($moneyBox, $data) {
            // Lock the row so concurrent withdrawal requests see the same balance
            $moneyBox = MoneyBox::lockForUpdate()->findOrFail($moneyBox->id);

            $availableBalance = $moneyBox->getAvailableBalance();

            if ($data->amount > $availableBalance) {
                throw new \InvalidArgumentException(
                    "Insufficient balance. Available: {$availableBalance}, Requested: {$data->amount}"
                );
            }

            $account = WithdrawalAccount::findOrFail($data->withdrawalAccountId);
            $feeData = $this->calculateFee->execute($data->amount);

            $withdrawal = MoneyBoxWithdrawal::create([
                'money_box_id'          => $moneyBox->id,
                'user_id'               => $moneyBox->user_id,
                'withdrawal_account_id' => $account->id,
                'amount'                => $data->amount,
                'fee'                   => $feeData->fee,
                'net_amount'            => $feeData->netAmount,
                'currency_code'         => $moneyBox->currency_code,
                'status'                => 'pending',
                'reference'             => MoneyBoxWithdrawal::generateReference(),
                'payment_provider'      => 'trendipay',
                'user_note'             => $data->note,
            ]);

            event(new WithdrawalRequested($withdrawal));

            return $withdrawal;
        });
    }
}