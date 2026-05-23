<?php

namespace App\Listeners;

use App\Events\WithdrawalRequested;
use App\Mail\WithdrawalSubmittedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWithdrawalSubmittedEmail implements ShouldQueue
{
    public function handle(WithdrawalRequested $event): void
    {
        $withdrawal = $event->withdrawal;
        $user = $withdrawal->user ?? null;
        if (!$user?->email) {
            Log::warning('SendWithdrawalSubmittedEmail: no user email', ['withdrawal_id' => $withdrawal->id]);
            return;
        }
        Mail::to($user->email)->send(new WithdrawalSubmittedMail($withdrawal));
    }
}