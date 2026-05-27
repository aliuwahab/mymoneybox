<?php

namespace App\Mail;

use App\Models\PiggyBox;
use App\Models\PiggyDonation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PiggyDonationReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly PiggyDonation $donation,
        public readonly PiggyBox $piggyBox,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Piggy Wallet gift receipt',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.piggy-donation-receipt',
        );
    }
}
