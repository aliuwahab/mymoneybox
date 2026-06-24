<?php

namespace App\Jobs;

use App\Mail\MoneyBoxNudgeMail;
use App\Models\MoneyBox;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMoneyBoxNudgeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly MoneyBox $moneyBox,
        public readonly string $step, // '24h', '5d', '10d'
    ) {}

    public function handle(): void
    {
        $owner = $this->moneyBox->user;

        if (! $owner?->email) {
            return;
        }

        // Don't nudge if the box has already been deleted
        if (! $this->moneyBox->exists) {
            return;
        }

        try {
            Mail::to($owner->email)->send(new MoneyBoxNudgeMail($this->moneyBox, $this->step));
        } catch (\Throwable $e) {
            Log::error('Failed to send MoneyBox nudge email', [
                'money_box_id' => $this->moneyBox->id,
                'step'         => $this->step,
                'error'        => $e->getMessage(),
            ]);
        }
    }
}