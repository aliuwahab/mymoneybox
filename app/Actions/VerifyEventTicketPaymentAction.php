<?php

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Enums\TicketStatus;
use App\Events\TicketIssued;
use App\Models\EventBoxTicket;
use App\Models\EventBoxTicketType;
use App\Payment\PaymentManager;

class VerifyEventTicketPaymentAction
{
    public function __construct(protected PaymentManager $paymentManager) {}

    /**
     * Verify payment for a ticket and complete its whole group if applicable.
     * Returns ['success' => bool, 'completed' => int, 'message' => string]
     */
    public function execute(EventBoxTicket $ticket): array
    {
        $reference = $ticket->payment_group ?: $ticket->payment_reference;

        $verification = $this->paymentManager->verifyPayment($reference);

        if (! $verification['success']) {
            return [
                'success' => false,
                'completed' => 0,
                'message' => $verification['message'] ?? 'Payment not confirmed by provider.',
            ];
        }

        // Find all still-pending tickets for this reference
        $pending = $ticket->payment_group
            ? EventBoxTicket::where('payment_group', $reference)
                ->where('payment_status', PaymentStatus::Pending)
                ->get()
            : EventBoxTicket::where('payment_reference', $reference)
                ->where('payment_status', PaymentStatus::Pending)
                ->get();

        $completedCount = 0;

        foreach ($pending as $t) {
            $affected = EventBoxTicket::where('id', $t->id)
                ->where('payment_status', '!=', PaymentStatus::Completed)
                ->update([
                    'payment_status' => PaymentStatus::Completed,
                    'status' => TicketStatus::Unused,
                    'code' => EventBoxTicket::generateCode(),
                    'transaction_rrn' => $verification['transaction_rrn'] ?? $t->transaction_rrn,
                    'payment_metadata' => array_merge($t->payment_metadata ?? [], [
                        'manual_verification' => [
                            'at' => now()->toDateTimeString(),
                            'reference' => $reference,
                            'raw_data' => $verification['raw_data'] ?? null,
                        ],
                    ]),
                ]);

            if (! $affected) {
                continue;
            }

            $t->eventBox->increment('tickets_sold');

            if ($t->ticket_type_id) {
                EventBoxTicketType::where('id', $t->ticket_type_id)->increment('sold');
            }

            event(new TicketIssued($t->fresh(['eventBox'])));

            $completedCount++;
        }

        return [
            'success' => true,
            'completed' => $completedCount,
            'message' => $completedCount > 0
                ? "Completed {$completedCount} ticket(s)."
                : 'Payment verified but no pending tickets to update.',
        ];
    }
}