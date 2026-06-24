<?php

namespace App\Mail;

use App\Models\MoneyBox;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MoneyBoxNudgeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly MoneyBox $moneyBox,
        public readonly string $step, // '24h', '5d', '10d'
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            '24h' => 'Have you shared your PiggyBox yet?',
            '5d'  => 'Day 5 — your PiggyBox still has room to grow',
            '10d' => 'Last nudge — keep the momentum going on "' . $this->moneyBox->title . '"',
        ];

        return new Envelope(subject: $subjects[$this->step] ?? 'A nudge from MyPiggyBox');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.money-box-nudge');
    }
}