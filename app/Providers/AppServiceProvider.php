<?php

namespace App\Providers;

use App\Events\ContributionProcessed;
use App\Events\MoneyBoxCreated;
use App\Events\TicketIssued;
use App\Events\WithdrawalApproved;
use App\Events\WithdrawalDisbursed;
use App\Events\WithdrawalRequested;
use App\Listeners\NotifyMoneyBoxOwner;
use App\Listeners\SendContributionThankYouEmail;
use App\Listeners\SendMoneyBoxCreatedNotification;
use App\Listeners\SendTicketEmail;
use App\Listeners\SendWithdrawalApprovedEmail;
use App\Listeners\SendWithdrawalDisbursedEmail;
use App\Listeners\SendWithdrawalSubmittedEmail;
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
        Event::listen(WithdrawalRequested::class, SendWithdrawalSubmittedEmail::class);

        // Fired when a withdrawal is approved by an admin
        Event::listen(WithdrawalApproved::class, SendWithdrawalApprovedEmail::class);

        // Fired when TrendiPay confirms a disbursement via webhook
        Event::listen(WithdrawalDisbursed::class, SendWithdrawalDisbursedEmail::class);

        // Fired when a ticket is issued after payment confirmation
        Event::listen(TicketIssued::class, SendTicketEmail::class);
    }
}