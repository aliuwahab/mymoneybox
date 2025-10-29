<?php

namespace App\Listeners;

use App\Events\MoneyBoxCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMoneyBoxCreatedNotification implements ShouldQueue
{
    public function handle(MoneyBoxCreated $event): void
    {
        // Send notification to money box creator
        // This can be implemented with Laravel Notifications
        // Example: $event->moneyBox->user->notify(new MoneyBoxCreatedNotification($event->moneyBox));
    }
}
