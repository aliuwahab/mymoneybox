<?php

namespace App\Mail;

use App\Models\MoneyBox;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PiggyBoxShareReminderMail extends Mailable
{
    use SerializesModels;

    public function __construct(public MoneyBox $moneyBox) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Share your PiggyBox and start collecting');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.box-share-reminder');
    }
}