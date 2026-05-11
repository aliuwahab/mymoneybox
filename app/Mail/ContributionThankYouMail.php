<?php

namespace App\Mail;

use App\Models\Contribution;
use App\Models\MoneyBox;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContributionThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Contribution $contribution,
        public readonly MoneyBox $moneyBox,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank you for your contribution to "' . $this->moneyBox->title . '"',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contribution-thank-you',
        );
    }
}