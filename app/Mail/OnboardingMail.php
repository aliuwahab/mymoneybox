<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OnboardingMail extends Mailable
{
    use SerializesModels;

    public function __construct(public User $user, public string $step) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'onboarding_1d'  => "You're all set \xe2\x80\x94 let's get you started",
            'onboarding_3d'  => 'Your PiggyWallet is ready to receive donations',
            'onboarding_7d'  => 'Ideas for your first PiggyBox',
            'onboarding_30d' => 'Still with us? One step can make a difference',
            'onboarding_90d' => 'One last nudge from us',
        ];

        return new Envelope(subject: $subjects[$this->step] ?? 'A note from MyPiggyBox');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.onboarding');
    }
}