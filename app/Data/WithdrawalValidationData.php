<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class WithdrawalValidationData extends Data
{
    public function __construct(
        public bool $valid,
        /** @var array<string> */
        public array $errors,
    ) {}
}
