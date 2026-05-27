<?php

namespace App\Http\Controllers;

use App\Actions\CreateEventBoxTicketRefundAction;
use App\Enums\PaymentStatus;
use App\Enums\TicketStatus;
use App\Events\TicketIssued;
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

        if (! $ticket) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Ticket not found for this event',
            ]);
        }

        if ($ticket->status === TicketStatus::Redeemed) {
            return response()->json([
                'status' => 'already_redeemed',
                'redeemed_at' => $ticket->redeemed_at?->toDateTimeString(),
                'holder_name' => $ticket->buyer_name,
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
            'status' => 'valid',
            'ticket_id' => $ticket->id,
            'holder_name' => $ticket->buyer_name,
            'holder_email' => $ticket->buyer_email,
            'code' => $ticket->code,
        ]);
    }

    /**
     * Void a ticket (owner only). Decrements sold counts and marks refunded.
     */
    public function void(Request $request, EventBox $eventBox, EventBoxTicket $ticket, CreateEventBoxTicketRefundAction $refundAction): JsonResponse
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        if ($ticket->event_box_id !== $eventBox->id) {
            return response()->json(['status' => 'error', 'message' => 'Ticket does not belong to this event.'], 422);
        }

        $validated = $request->validate([
            'code_confirmation' => ['required', 'string'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! hash_equals((string) $ticket->code, trim($validated['code_confirmation']))) {
            return response()->json(['status' => 'error', 'message' => 'Ticket code confirmation does not match.'], 422);
        }

        if ($ticket->status === TicketStatus::Voided) {
            return response()->json(['status' => 'error', 'message' => 'Ticket is already voided.'], 422);
        }

        if ($ticket->status === TicketStatus::Redeemed) {
            return response()->json(['status' => 'error', 'message' => 'Redeemed tickets cannot be voided.'], 422);
        }

        if ($ticket->payment_status !== PaymentStatus::Completed || $ticket->status !== TicketStatus::Unused) {
            return response()->json(['status' => 'error', 'message' => 'Only unused paid tickets can be voided.'], 422);
        }

        try {
            $refund = $refundAction->execute($ticket, auth()->id(), $validated['reason'] ?? null);
        } catch (\RuntimeException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'status' => 'voided',
            'message' => 'Ticket voided and refund queued.',
            'refund_reference' => $refund->reference,
            'refund_amount' => (float) $refund->refund_amount,
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
                'status' => 'error',
                'message' => 'Ticket does not belong to this event',
            ], 422);
        }

        if (! $ticket->isRedeemable()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket is not redeemable',
            ], 422);
        }

        $ticket->redeem(auth()->id());

        activity('eventbox')
            ->performedOn($eventBox)
            ->causedBy(auth()->user())
            ->event('ticket_redeemed')
            ->withProperties([
                'ticket_id' => $ticket->id,
                'ticket_code' => $ticket->code,
                'ip_address' => $request->ip(),
            ])
            ->log('Ticket redeemed');

        return response()->json([
            'status' => 'redeemed',
            'message' => 'Ticket redeemed successfully',
        ]);
    }

    /**
     * Resend the ticket confirmation email (owner only).
     */
    public function resendEmail(Request $request, EventBox $eventBox, EventBoxTicket $ticket): JsonResponse
    {
        abort_if(auth()->id() !== $eventBox->user_id, 403);

        if ($ticket->event_box_id !== $eventBox->id) {
            return response()->json(['status' => 'error', 'message' => 'Ticket does not belong to this event.'], 422);
        }

        if ($ticket->payment_status !== PaymentStatus::Completed || ! $ticket->code) {
            return response()->json(['status' => 'error', 'message' => 'Only paid tickets with a code can have their email resent.'], 422);
        }

        $ticket->update([
            'ticket_email_sending_at' => null,
            'ticket_email_sent_at'    => null,
        ]);

        event(new TicketIssued($ticket->fresh(['eventBox'])));

        return response()->json(['status' => 'success', 'message' => 'Ticket email queued for resend.']);
    }
}
