<?php

namespace App\Listeners;

use App\Events\ContributionProcessed;
use App\Mail\ContributionThankYouMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendContributionThankYouEmail implements ShouldQueue
{
    public function handle(ContributionProcessed $event): void
    {
        $contribution = $event->contribution;

        if (! $contribution->contributor_email) {
            return;
        }

        try {
            Mail::to($contribution->contributor_email)
                ->send(new ContributionThankYouMail($contribution, $event->moneyBox));

            $contribution->forceFill([
                'receipt_sent_at' => $contribution->receipt_sent_at ?? now(),
            ])->save();
        } catch (\Throwable $e) {
            Log::error('Failed to send contribution thank-you email', [
                'contribution_id' => $contribution->id,
                'email' => $contribution->contributor_email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
