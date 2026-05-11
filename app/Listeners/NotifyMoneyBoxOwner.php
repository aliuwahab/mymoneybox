<?php

namespace App\Listeners;

use App\Events\ContributionProcessed;
use App\Mail\NewContributionMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyMoneyBoxOwner implements ShouldQueue
{
    public function handle(ContributionProcessed $event): void
    {
        $moneyBox = $event->moneyBox;
        $owner    = $moneyBox->user;

        if (!$owner?->email) {
            return;
        }

        try {
            Mail::to($owner->email)
                ->send(new NewContributionMail($event->contribution, $moneyBox));
        } catch (\Throwable $e) {
            Log::error('Failed to notify box owner of contribution', [
                'money_box_id'    => $moneyBox->id,
                'owner_id'        => $owner->id,
                'contribution_id' => $event->contribution->id,
                'error'           => $e->getMessage(),
            ]);
        }
    }
}