<?php

namespace App\Listeners;

use App\Events\ContributionProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyMoneyBoxOwner implements ShouldQueue
{
    public function handle(ContributionProcessed $event): void
    {
        // Notify money box owner about new contribution
        // Example: $event->moneyBox->user->notify(new NewContributionNotification($event->contribution));
    }
}
