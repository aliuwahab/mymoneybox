<?php

namespace App\Mail;

use App\Models\MoneyBoxWithdrawal;
use App\Models\PiggyBoxWithdrawal;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalDisbursedMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public MoneyBoxWithdrawal|PiggyBoxWithdrawal $withdrawal
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your withdrawal has been sent — ' . $this->withdrawal->reference);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.withdrawal-disbursed');
    }
}