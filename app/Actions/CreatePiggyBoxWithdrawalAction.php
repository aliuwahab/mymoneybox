<?php

namespace App\Actions;

use App\Models\PiggyBox;
use App\Models\PiggyBoxWithdrawal;
use App\Models\WithdrawalAccount;

class CreatePiggyBoxWithdrawalAction
{
    public function __construct(
        protected CalculateWithdrawalFeeAction $calculateFee,
    ) {}

    public function execute(
        PiggyBox $piggyBox,
        WithdrawalAccount $account,
        float $amount,
        ?string $note = null
    ): PiggyBoxWithdrawal {
        $feeData = $this->calculateFee->execute($amount);

        return PiggyBoxWithdrawal::create([
            'piggy_box_id' => $piggyBox->id,
            'user_id' => $piggyBox->user_id,
            'withdrawal_account_id' => $account->id,
            'amount' => $amount,
            'fee' => $feeData->fee,
            'net_amount' => $feeData->netAmount,
            'currency_code' => $piggyBox->currency_code,
            'status' => 'pending',
            'reference' => PiggyBoxWithdrawal::generateReference(),
            'payment_provider' => 'trendipay',
            'user_note' => $note,
        ]);
    }
}
