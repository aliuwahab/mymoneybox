<?php

namespace App\Listeners;

use App\Events\MoneyBoxCreated;
use App\Mail\MoneyBoxCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMoneyBoxCreatedNotification implements ShouldQueue
{
    public function handle(MoneyBoxCreated $event): void
    {
        $moneyBox = $event->moneyBox;
        $owner    = $moneyBox->user;

        if (!$owner?->email) {
            return;
        }

        try {
            Mail::to($owner->email)
                ->send(new MoneyBoxCreatedMail($moneyBox));
        } catch (\Throwable $e) {
            Log::error('Failed to send money box created notification', [
                'money_box_id' => $moneyBox->id,
                'owner_id'     => $owner->id,
                'error'        => $e->getMessage(),
            ]);
        }
    }
}