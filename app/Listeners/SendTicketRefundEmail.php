<?php

namespace App\Listeners;

use App\Events\TicketRefundQueued;
use App\Mail\TicketRefundMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTicketRefundEmail implements ShouldQueue
{
    public function handle(TicketRefundQueued $event): void
    {
        $refund = $event->refund->load(['ticket.eventBox']);

        $buyerEmail = $refund->ticket->buyer_email ?? null;

        if (! $buyerEmail) {
            Log::warning('SendTicketRefundEmail: no buyer email', ['refund_id' => $refund->id]);

            return;
        }

        Mail::to($buyerEmail)->send(new TicketRefundMail($refund));
    }
}