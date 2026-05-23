<?php

namespace App\Console\Commands;

use App\Mail\PiggyBoxShareReminderMail;
use App\Mail\OnboardingMail;
use App\Models\MoneyBox;
use App\Models\User;
use App\Models\UserMarketingEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendMarketingEmails extends Command
{
    protected $signature = 'app:send-marketing-emails';
    protected $description = 'Send scheduled onboarding and engagement marketing emails';

    public function handle(): void
    {
        $this->sendOnboardingEmails();
        $this->sendBoxShareReminders();
    }

    private function sendOnboardingEmails(): void
    {
        $intervals = [
            'onboarding_1d'  => 1,
            'onboarding_3d'  => 3,
            'onboarding_7d'  => 7,
            'onboarding_30d' => 30,
            'onboarding_90d' => 90,
        ];

        foreach ($intervals as $key => $days) {
            $users = User::query()
                ->whereNotNull('email_verified_at')
                ->whereDoesntHave('moneyBoxes')
                ->whereDate('created_at', now()->subDays($days)->toDateString())
                ->whereDoesntHave('marketingEmails', fn($q) => $q->where('email_key', $key))
                ->get();

            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->queue(new OnboardingMail($user, $key));
                    UserMarketingEmail::create(['user_id' => $user->id, 'email_key' => $key, 'sent_at' => now()]);
                    $this->line("  Onboarding [{$key}] → {$user->email}");
                } catch (\Exception $e) {
                    Log::error('SendMarketingEmails: onboarding failed', ['key' => $key, 'user' => $user->id, 'error' => $e->getMessage()]);
                }
            }
        }
    }

    private function sendBoxShareReminders(): void
    {
        $boxes = MoneyBox::with('user')
            ->where('is_active', true)
            ->whereDate('created_at', now()->subDay()->toDateString())
            ->where(function($q) {
                $q->where('is_ongoing', true)
                  ->orWhereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->whereHas('user', fn($q) => $q->whereNotNull('email_verified_at'))
            ->get()
            ->filter(function($box) {
                $key = 'box_share_' . $box->id;
                return !UserMarketingEmail::where('user_id', $box->user_id)->where('email_key', $key)->exists();
            });

        foreach ($boxes as $box) {
            try {
                Mail::to($box->user->email)->queue(new PiggyBoxShareReminderMail($box));
                UserMarketingEmail::create([
                    'user_id'   => $box->user_id,
                    'email_key' => 'box_share_' . $box->id,
                    'sent_at'   => now(),
                ]);
                $this->line("  BoxShare → {$box->user->email} (box: {$box->slug})");
            } catch (\Exception $e) {
                Log::error('SendMarketingEmails: box share failed', ['box' => $box->id, 'error' => $e->getMessage()]);
            }
        }
    }
}