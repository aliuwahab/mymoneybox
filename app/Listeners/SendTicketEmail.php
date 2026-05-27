<?php

namespace App\Listeners;

use App\Events\TicketIssued;
use App\Mail\TicketMail;
use App\Models\EventBoxTicket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendTicketEmail implements ShouldQueue
{
    public function handle(TicketIssued $event): void
    {
        $ticket = $event->ticket->fresh(['eventBox']);

        if (! $ticket || $ticket->ticket_email_sent_at) {
            return;
        }

        $now = now();
        $claimed = EventBoxTicket::query()
            ->whereKey($ticket->id)
            ->whereNull('ticket_email_sending_at')
            ->whereNull('ticket_email_sent_at')
            ->update([
                'ticket_email_sending_at' => $now,
                'ticket_email_sent_at' => $now,
            ]);

        if (! $claimed) {
            return;
        }

        try {
            Mail::to($ticket->buyer_email)
                ->send(new TicketMail($ticket));

            EventBoxTicket::query()
                ->whereKey($ticket->id)
                ->update(['ticket_email_sending_at' => null]);
        } catch (Throwable $e) {
            EventBoxTicket::query()
                ->whereKey($ticket->id)
                ->update([
                    'ticket_email_sending_at' => null,
                    'ticket_email_sent_at' => null,
                ]);

            throw $e;
        }
    }
}
