<?php

namespace App\Events;

use App\Models\MoneyBoxWithdrawal;
use App\Models\PiggyBoxWithdrawal;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WithdrawalDisbursed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public MoneyBoxWithdrawal|PiggyBoxWithdrawal $withdrawal
    ) {}
}