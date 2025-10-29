<?php

namespace App\Actions;

use App\Events\MoneyBoxStatsUpdated;
use App\Models\Contribution;
use App\Models\MoneyBox;

class UpdateMoneyBoxStatsAction
{
    public function execute(MoneyBox $moneyBox, Contribution $contribution): void
    {
        $moneyBox->increment('total_contributions', $contribution->amount);
        $moneyBox->increment('contribution_count');

        event(new MoneyBoxStatsUpdated($moneyBox, $contribution));
    }
}
