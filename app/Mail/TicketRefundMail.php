<?php

namespace App\Mail;

use App\Models\EventBoxTicketRefund;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketRefundMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EventBoxTicketRefund $refund) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Refund queued for your {$this->refund->ticket->eventBox->title} ticket",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-refund',
            with: [
                'refund'   => $this->refund,
                'ticket'   => $this->refund->ticket,
                'eventBox' => $this->refund->ticket->eventBox,
            ],
        );
    }
}