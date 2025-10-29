<?php

namespace App\Providers;

use App\Events\ContributionProcessed;
use App\Events\MoneyBoxCreated;
use App\Listeners\NotifyMoneyBoxOwner;
use App\Listeners\SendContributionThankYouEmail;
use App\Listeners\SendMoneyBoxCreatedNotification;
use App\Payment\PaymentManager;
use App\Payment\Providers\PaystackProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Payment Manager
        $this->app->singleton(PaymentManager::class, function ($app) {
            $manager = new PaymentManager();

            // Register Paystack provider
            $manager->extend('paystack', new PaystackProvider());

            return $manager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register event listeners
        Event::listen(
            MoneyBoxCreated::class,
            SendMoneyBoxCreatedNotification::class
        );

        Event::listen(
            ContributionProcessed::class,
            [SendContributionThankYouEmail::class, NotifyMoneyBoxOwner::class]
        );
    }
}
