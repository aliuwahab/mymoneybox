<?php

namespace App\Providers;

use App\Events\ContributionProcessed;
use App\Events\MoneyBoxCreated;
use App\Events\WithdrawalRequested;
use App\Listeners\NotifyMoneyBoxOwner;
use App\Listeners\SendContributionThankYouEmail;
use App\Listeners\SendMoneyBoxCreatedNotification;
use App\Payment\PaymentManager;
use App\Payment\Providers\TrendiPayProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class, function () {
            $manager = new PaymentManager();
            $manager->extend('trendipay', new TrendiPayProvider());
            return $manager;
        });
    }

    public function boot(): void
    {
        // Fired after a contribution payment is confirmed by the TrendiPay webhook
        Event::listen(ContributionProcessed::class, [
            SendContributionThankYouEmail::class,
            NotifyMoneyBoxOwner::class,
        ]);

        // Fired after a new MoneyBox is created (e.g. welcome email, admin alert)
        Event::listen(MoneyBoxCreated::class, SendMoneyBoxCreatedNotification::class);

        // Fired when a withdrawal request is submitted (ready for admin review)
        // Wire in your notification logic as needed, e.g.:
        // Event::listen(WithdrawalRequested::class, NotifyAdminOfWithdrawal::class);
        Event::listen(WithdrawalRequested::class, function (WithdrawalRequested $event) {
            // placeholder — add a queued listener when notification infra is ready
        });
    }
}