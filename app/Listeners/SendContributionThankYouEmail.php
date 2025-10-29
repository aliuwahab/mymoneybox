<?php

namespace App\Listeners;

use App\Events\ContributionProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendContributionThankYouEmail implements ShouldQueue
{
    public function handle(ContributionProcessed $event): void
    {
        // Send thank you email to contributor
        // Send notification to money box owner about new contribution

        // Example:
        // if ($event->contribution->contributor_email) {
        //     Mail::to($event->contribution->contributor_email)
        //         ->send(new ContributionThankYouMail($event->contribution, $event->moneyBox));
        // }
    }
}
