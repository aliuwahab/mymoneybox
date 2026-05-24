<?php

namespace App\Listeners;

use App\Events\TicketIssued;
use App\Mail\TicketMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendTicketEmail implements ShouldQueue
{
    public function handle(TicketIssued $event): void
    {
        Mail::to($event->ticket->buyer_email)
            ->send(new TicketMail($event->ticket));
    }
}