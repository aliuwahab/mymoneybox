<?php

namespace App\Events;

use App\Models\MoneyBox;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MoneyBoxCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public MoneyBox $moneyBox
    ) {}
}
