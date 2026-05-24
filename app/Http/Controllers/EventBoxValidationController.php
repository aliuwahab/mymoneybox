<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Enums\TicketStatus;
use App\Models\EventBox;
use App\Models\EventBoxTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventBoxValidationController extends Controller
{
    /**
     * Validate a ticket code for an event (owner only).
     */
    public function validate(Request $request, EventBox $eventBox): JsonResponse
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $ticket = EventBoxTicket::where('code', $request->input('code'))
            ->where('event_box_id', $eventBox->id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status'  => 'not_found',
                'message' => 'Ticket not found for this event',
            ]);
        }

        if ($ticket->status === TicketStatus::Redeemed) {
            return response()->json([
                'status'       => 'already_redeemed',
                'redeemed_at'  => $ticket->redeemed_at?->toDateTimeString(),
                'holder_name'  => $ticket->buyer_name,
            ]);
        }

        if ($ticket->status === TicketStatus::Voided) {
            return response()->json([
                'status' => 'voided',
            ]);
        }

        if ($ticket->payment_status !== PaymentStatus::Completed) {
            return response()->json([
                'status' => 'payment_pending',
            ]);
        }

        return response()->json([
            'status'       => 'valid',
            'ticket_id'    => $ticket->id,
            'holder_name'  => $ticket->buyer_name,
            'holder_email' => $ticket->buyer_email,
            'code'         => $ticket->code,
        ]);
    }

    /**
     * Void a ticket (owner only). Decrements sold counts and marks refunded.
     */
    public function void(Request $request, EventBox $eventBox, EventBoxTicket $ticket): JsonResponse
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        if ($ticket->event_box_id !== $eventBox->id) {
            return response()->json(['status' => 'error', 'message' => 'Ticket does not belong to this event.'], 422);
        }

        if ($ticket->status === TicketStatus::Voided) {
            return response()->json(['status' => 'error', 'message' => 'Ticket is already voided.'], 422);
        }

        $wasCompleted = $ticket->payment_status === PaymentStatus::Completed;

        $ticket->update([
            'status'         => TicketStatus::Voided,
            'payment_status' => PaymentStatus::Refunded,
        ]);

        if ($wasCompleted) {
            $eventBox->decrement('tickets_sold');

            if ($ticket->ticket_type_id) {
                \App\Models\EventBoxTicketType::where('id', $ticket->ticket_type_id)
                    ->where('sold', '>', 0)
                    ->decrement('sold');
            }

            // Restore sold_out event back to active
            if ($eventBox->fresh()->status->value === 'sold_out') {
                $eventBox->update(['status' => 'active']);
            }
        }

        return response()->json(['status' => 'voided', 'message' => 'Ticket voided successfully.']);
    }

    /**
     * Redeem a ticket (owner only).
     */
    public function redeem(Request $request, EventBox $eventBox, EventBoxTicket $ticket): JsonResponse
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        if ($ticket->event_box_id !== $eventBox->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ticket does not belong to this event',
            ], 422);
        }

        if (!$ticket->isRedeemable()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ticket is not redeemable',
            ], 422);
        }

        $ticket->redeem(auth()->id());

        return response()->json([
            'status'  => 'redeemed',
            'message' => 'Ticket redeemed successfully',
        ]);
    }
}