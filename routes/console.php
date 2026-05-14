<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test {email}', function (string $email) {
    Mail::raw('This is a MyPiggyBox production email test.', function (Message $message) use ($email) {
        $message->to($email)
            ->subject('MyPiggyBox email test');
    });

    $this->info("Test email sent to {$email}.");
})->purpose('Send a test email using the configured mailer');
