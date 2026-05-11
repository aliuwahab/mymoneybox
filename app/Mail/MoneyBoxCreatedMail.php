<?php

namespace App\Mail;

use App\Models\MoneyBox;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MoneyBoxCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly MoneyBox $moneyBox,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your box "' . $this->moneyBox->title . '" is live on MyMoneyBox',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.money-box-created',
        );
    }
}