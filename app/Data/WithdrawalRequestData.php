<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class WithdrawalRequestData extends Data
{
    public function __construct(
        public readonly float $amount,
        public readonly int $withdrawalAccountId,
        public readonly ?string $note = null,
    ) {}
}