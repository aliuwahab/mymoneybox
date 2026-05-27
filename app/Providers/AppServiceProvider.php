<?php

namespace App\Providers;

use App\Payment\PaymentManager;
use App\Payment\Providers\TrendiPayProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class, function () {
            $manager = new PaymentManager;
            $manager->extend('trendipay', new TrendiPayProvider);

            return $manager;
        });
    }
}
