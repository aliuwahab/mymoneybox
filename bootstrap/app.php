<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Exclude webhook routes from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Automatic disbursement disabled — use the admin Disburse button to send funds manually.
        // Re-enable these once volume justifies twice-daily batch runs.
        // $schedule->command('withdrawals:disburse')->dailyAt('08:00')->withoutOverlapping()->runInBackground();
        // $schedule->command('withdrawals:disburse')->dailyAt('19:00')->withoutOverlapping()->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'CSRF token mismatch.'], 419);
            }

            return redirect()->route('login')
                ->with('status', 'Your session expired. Please log in again.');
        });
    })->create();
