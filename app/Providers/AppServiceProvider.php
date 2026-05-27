<?php

namespace App\Providers;

use App\Payment\PaymentManager;
use App\Payment\Providers\TrendiPayProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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

    public function boot(): void
    {
        RateLimiter::for('piggy-lookup', function (Request $request) {
            $code = Str::upper((string) $request->input('piggy_code', $request->route('code', '')));
            $codeKey = $code !== '' ? sha1($code.'|'.$request->ip()) : $request->ip();

            return [
                Limit::perMinute(6)->by($request->ip()),
                Limit::perHour(20)->by($codeKey),
            ];
        });

        RateLimiter::for('piggy-public', function (Request $request) {
            $code = Str::upper((string) $request->route('code', ''));

            return [
                Limit::perMinute(20)->by($request->ip()),
                Limit::perHour(60)->by(sha1($code.'|'.$request->ip())),
            ];
        });
    }
}
