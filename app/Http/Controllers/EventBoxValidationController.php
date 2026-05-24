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