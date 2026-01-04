<?php

namespace App\Actions;

use App\Models\MoneyBox;
use App\Models\MoneyBoxWithdrawal;
use App\Models\WithdrawalAccount;

class CreateMoneyBoxWithdrawalAction
{
    public function __construct(
        protected CalculateWithdrawalFeeAction $calculateFee,
    ) {}

    public function execute(
        MoneyBox $moneyBox,
        WithdrawalAccount $account,
        float $amount,
        ?string $note = null
    ): MoneyBoxWithdrawal {
        $feeData = $this->calculateFee->execute($amount);

        return MoneyBoxWithdrawal::create([
            'money_box_id' => $moneyBox->id,
            'user_id' => $moneyBox->user_id,
            'withdrawal_account_id' => $account->id,
            'amount' => $amount,
            'fee' => $feeData->fee,
            'net_amount' => $feeData->netAmount,
            'currency_code' => $moneyBox->currency_code,
            'status' => 'pending',
            'reference' => MoneyBoxWithdrawal::generateReference(),
            'payment_provider' => 'trendipay',
            'user_note' => $note,
        ]);
    }
}
