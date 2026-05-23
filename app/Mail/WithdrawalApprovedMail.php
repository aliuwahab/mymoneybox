<?php

namespace App\Mail;

use App\Models\MoneyBoxWithdrawal;
use App\Models\PiggyBoxWithdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly MoneyBoxWithdrawal|PiggyBoxWithdrawal $withdrawal,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Withdrawal Has Been Approved');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.withdrawal-approved');
    }
}