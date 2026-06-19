<?php

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Enums\TicketStatus;
use App\Events\TicketRefundQueued;
use App\Models\EventBoxTicket;
use App\Models\EventBoxTicketRefund;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateEventBoxTicketRefundAction
{
    public function execute(EventBoxTicket $ticket, int $userId, ?string $reason = null): EventBoxTicketRefund
    {
        return DB::transaction(function () use ($ticket, $userId, $reason) {
            $ticket = EventBoxTicket::query()
                ->with(['eventBox', 'refund'])
                ->lockForUpdate()
                ->findOrFail($ticket->id);

            if (! $ticket->isVoidable()) {
                throw new \RuntimeException('Only unused paid tickets can be voided.');
            }

            if ($ticket->refund) {
                return $ticket->refund;
            }

            $grossAmount = (float) $ticket->amount;
            $chargeAmount = round($grossAmount * ($ticket->eventBox->getEffectiveFeePercentage() / 100), 2);
            $refundAmount = max(0, round($grossAmount - $chargeAmount, 2));

            $refund = EventBoxTicketRefund::create([
                'event_box_ticket_id' => $ticket->id,
                'requested_by' => $userId,
                'reference' => $this->generateReference(),
                'gross_amount' => $grossAmount,
                'charge_amount' => $chargeAmount,
                'refund_amount' => $refundAmount,
                'currency_code' => 'GHS',
                'recipient_account_number' => $ticket->payment_account_number,
                'recipient_network' => $this->normalizeNetwork($ticket->payment_method),
                'recipient_name' => $ticket->buyer_name,
                'payment_provider' => 'trendipay',
                'reason' => $reason,
                'requested_ip_address' => request()?->ip(),
                'requested_user_agent' => request()?->userAgent(),
            ]);

            $ticket->update([
                'status' => TicketStatus::Voided,
                'payment_status' => PaymentStatus::Refunded,
                'voided_at' => now(),
                'voided_by' => $userId,
                'void_reason' => $reason,
            ]);

            $ticket->eventBox()
                ->where('tickets_sold', '>', 0)
                ->decrement('tickets_sold');

            if ($ticket->ticket_type_id) {
                $ticket->ticketType()
                    ->where('sold', '>', 0)
                    ->decrement('sold');
            }

            if ($ticket->eventBox->fresh()->status->value === 'sold_out') {
                $ticket->eventBox->update(['status' => 'active']);
            }

            activity('eventbox')
                ->performedOn($ticket->eventBox)
                ->causedBy(auth()->user())
                ->event('ticket_voided')
                ->withProperties([
                    'ticket_id' => $ticket->id,
                    'ticket_code' => $ticket->code,
                    'refund_id' => $refund->id,
                    'refund_reference' => $refund->reference,
                    'gross_amount' => $grossAmount,
                    'charge_amount' => $chargeAmount,
                    'refund_amount' => $refundAmount,
                    'ip_address' => request()?->ip(),
                ])
                ->log('Ticket voided and refund queued');

            event(new TicketRefundQueued($refund->load(['ticket.eventBox'])));

            return $refund;
        });
    }

    private function generateReference(): string
    {
        do {
            $reference = 'ERF-'.strtoupper(Str::random(16));
        } while (EventBoxTicketRefund::where('reference', $reference)->exists());

        return $reference;
    }

    private function normalizeNetwork(?string $paymentMethod): ?string
    {
        if (! $paymentMethod) {
            return null;
        }

        $value = strtolower($paymentMethod);

        return match (true) {
            str_contains($value, 'mtn') => 'mtn',
            str_contains($value, 'vodafone'), str_contains($value, 'telecel') => 'vodafone',
            str_contains($value, 'airtel'), str_contains($value, 'tigo') => 'airteltigo',
            default => $value,
        };
    }
}
