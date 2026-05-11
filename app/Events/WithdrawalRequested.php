<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WithdrawalRequested
{
    use Dispatchable, SerializesModels;

    /**
     * @param object $withdrawal MoneyBoxWithdrawal or PiggyBoxWithdrawal
     */
    public function __construct(
        public readonly object $withdrawal,
    ) {}
}