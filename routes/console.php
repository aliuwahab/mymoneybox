<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test {email}', function (string $email) {
    $mailId = 'mpb-' . Str::lower(Str::random(10));
    $mailer = config('mail.default');
    $from = config('mail.from.address');
    $host = config("mail.mailers.{$mailer}.host", 'n/a');
    $port = config("mail.mailers.{$mailer}.port", 'n/a');
    $queue = config('queue.default');

    $this->line("Mail test ID: {$mailId}");
    $this->line("Mailer: {$mailer}");
    $this->line("SMTP host: {$host}");
    $this->line("SMTP port: {$port}");
    $this->line("From: {$from}");
    $this->line("Queue connection: {$queue}");

    Mail::raw(
        "This is a MyPiggyBox production email test.\n\nTest ID: {$mailId}\nSent at: " . now()->toIso8601String(),
        function (Message $message) use ($email, $from, $mailId) {
            $message->to($email)
                ->from($from, config('mail.from.name'))
                ->subject("MyPiggyBox email test [{$mailId}]");
        }
    );

    $this->info("SMTP accepted the test email for {$email}. Search for {$mailId} in your provider logs.");
})->purpose('Send a test email using the configured mailer');

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::call(fn() => Log::info('Scheduler: heartbeat', ['at' => now()->toDateTimeString()]))->everyMinute();

Schedule::command('app:send-marketing-emails')->dailyAt('08:00');
Schedule::command('tickets:recover')->everyFiveMinutes();

