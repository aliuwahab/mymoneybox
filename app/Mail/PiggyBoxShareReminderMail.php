<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PiggyBoxShareReminderMail extends Mailable
{
    use SerializesModels;

    public Collection $moneyBoxes;

    public function __construct(Collection $moneyBoxes)
    {
        $this->moneyBoxes = $moneyBoxes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Share your PiggyBox and start collecting');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.box-share-reminder');
    }
}