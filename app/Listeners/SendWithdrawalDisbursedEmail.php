<?php

namespace App\Listeners;

use App\Events\WithdrawalDisbursed;
use App\Mail\WithdrawalDisbursedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendWithdrawalDisbursedEmail implements ShouldQueue
{
    public function handle(WithdrawalDisbursed $event): void
    {
        $user = $event->withdrawal->user;

        if (!$user?->email) {
            return;
        }

        Mail::to($user->email)->send(new WithdrawalDisbursedMail($event->withdrawal));
    }
}