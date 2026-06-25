<?php

namespace App\Actions;

use App\Data\WithdrawalFeeData;

class CalculateWithdrawalFeeAction
{
    public function execute(float $amount, ?float $feePercentageOverride = null): WithdrawalFeeData
    {
        $feePercentage = $feePercentageOverride ?? config('withdrawal.fee_percentage', 1.5);
        $minFee = config('withdrawal.min_fee', 2);

        $fee = max($minFee, round(($amount * $feePercentage) / 100, 2));
        $netAmount = round($amount - $fee, 2);

        return new WithdrawalFeeData(
            amount: $amount,
            fee: $fee,
            feePercentage: $feePercentage,
            netAmount: $netAmount,
        );
    }
}
