<?php

namespace App\Events;

use App\Models\Contribution;
use App\Models\MoneyBox;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContributionProcessed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Contribution $contribution,
        public MoneyBox $moneyBox
    ) {}
}
