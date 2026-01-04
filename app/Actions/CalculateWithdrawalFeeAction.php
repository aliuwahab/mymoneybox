<?php

namespace App\Actions;

use App\Data\WithdrawalFeeData;

class CalculateWithdrawalFeeAction
{
    public function execute(float $amount): WithdrawalFeeData
    {
        $feePercentage = config('withdrawal.fee_percentage', 2.5);
        $minFee = config('withdrawal.min_fee', 2);
        $maxFee = config('withdrawal.max_fee', 20);

        // Calculate percentage fee
        $fee = ($amount * $feePercentage) / 100;

        // Apply minimum and maximum constraints
        $fee = max($minFee, min($maxFee, $fee));

        // Round to 2 decimal places
        $fee = round($fee, 2);
        $netAmount = round($amount - $fee, 2);

        return new WithdrawalFeeData(
            amount: $amount,
            fee: $fee,
            feePercentage: $feePercentage,
            netAmount: $netAmount,
        );
    }
}
