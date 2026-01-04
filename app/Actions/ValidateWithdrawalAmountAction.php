<?php

namespace App\Actions;

use App\Data\WithdrawalValidationData;

class ValidateWithdrawalAmountAction
{
    public function execute(float $amount, float $availableBalance): WithdrawalValidationData
    {
        $minAmount = config('withdrawal.min_amount', 10);
        
        $errors = [];

        if ($amount <= 0) {
            $errors[] = "Amount must be greater than zero";
        }

        if ($amount < $minAmount) {
            $errors[] = "Minimum withdrawal amount is " . number_format($minAmount, 2);
        }

        if ($amount > $availableBalance) {
            $errors[] = "Insufficient balance. Available: " . number_format($availableBalance, 2);
        }

        return new WithdrawalValidationData(
            valid: empty($errors),
            errors: $errors,
        );
    }

    public function getMinimumAmount(): float
    {
        return config('withdrawal.min_amount', 10);
    }
}
