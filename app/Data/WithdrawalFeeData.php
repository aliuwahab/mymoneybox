<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class WithdrawalFeeData extends Data
{
    public function __construct(
        public float $amount,
        public float $fee,
        public float $feePercentage,
        public float $netAmount,
    ) {}
}
