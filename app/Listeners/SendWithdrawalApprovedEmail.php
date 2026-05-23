<?php

namespace App\Listeners;

use App\Events\WithdrawalApproved;
use App\Mail\WithdrawalApprovedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWithdrawalApprovedEmail implements ShouldQueue
{
    public function handle(WithdrawalApproved $event): void
    {
        $withdrawal = $event->withdrawal;
        $user = $withdrawal->user ?? null;
        if (!$user?->email) {
            Log::warning('SendWithdrawalApprovedEmail: no user email', ['withdrawal_id' => $withdrawal->id]);
            return;
        }
        Mail::to($user->email)->send(new WithdrawalApprovedMail($withdrawal));
    }
}