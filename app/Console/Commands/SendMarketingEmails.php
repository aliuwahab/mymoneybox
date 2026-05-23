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
        $targetDate = now()->subDay()->toDateString();
        $emailKey   = 'box_share_' . $targetDate;

        $byUser = MoneyBox::with('user')
            ->where('is_active', true)
            ->whereDate('created_at', $targetDate)
            ->where(function($q) {
                $q->where('is_ongoing', true)
                  ->orWhereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->whereHas('user', fn($q) => $q->whereNotNull('email_verified_at'))
            ->get()
            ->groupBy('user_id');

        foreach ($byUser as $userId => $userBoxes) {
            if (UserMarketingEmail::where('user_id', $userId)->where('email_key', $emailKey)->exists()) {
                continue;
            }

            $user = $userBoxes->first()->user;

            try {
                Mail::to($user->email)->queue(new PiggyBoxShareReminderMail($userBoxes));
                UserMarketingEmail::create(['user_id' => $userId, 'email_key' => $emailKey, 'sent_at' => now()]);
                $this->line("  BoxShare → {$user->email} ({$userBoxes->count()} box(es))");
            } catch (\Exception $e) {
                Log::error('SendMarketingEmails: box share failed', ['user' => $userId, 'error' => $e->getMessage()]);
            }
        }
    }
}