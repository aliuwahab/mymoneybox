<?php

namespace App\Exports;

use App\Enums\PaymentStatus;
use App\Models\EventBox;
use App\Models\EventBoxTicket;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventBoxAttendeesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(
        private readonly EventBox $eventBox,
        private readonly array $filters = [],
    ) {}

    public function query(): Builder
    {
        $query = EventBoxTicket::query()
            ->where('event_box_id', $this->eventBox->id)
            ->whereIn('payment_status', [PaymentStatus::Completed->value, PaymentStatus::Refunded->value])
            ->with(['ticketType', 'refund'])
            ->latest();

        $search = trim((string) ($this->filters['q'] ?? ''));
        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('buyer_name', 'like', "%{$search}%")
                    ->orWhere('buyer_email', 'like', "%{$search}%")
                    ->orWhere('buyer_phone', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $status = $this->filters['status'] ?? 'all';
        if (in_array($status, ['unused', 'redeemed', 'voided'], true)) {
            $query->where('status', $status);
        }

        $ticketType = $this->filters['ticket_type'] ?? 'all';
        if ($ticketType !== 'all' && $ticketType !== null && $ticketType !== '') {
            $query->where('ticket_type_id', (int) $ticketType);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Buyer name',
            'Buyer email',
            'Buyer phone',
            'Ticket type',
            'Ticket code',
            'Status',
            'Redeemed at',
            'Voided at',
            'Gross amount',
            'Refund amount',
            'Refund status',
            'Payment reference',
            'Payment method',
            'Payment account',
            'Purchased at',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->buyer_name,
            $ticket->buyer_email,
            $ticket->buyer_phone,
            $ticket->ticket_type_name ?? $ticket->ticketType?->name,
            $ticket->code,
            $ticket->status->label(),
            $ticket->redeemed_at?->toDateTimeString(),
            $ticket->voided_at?->toDateTimeString(),
            (float) $ticket->amount,
            $ticket->refund ? (float) $ticket->refund->refund_amount : null,
            $ticket->refund?->status->label(),
            $ticket->payment_reference,
            $ticket->payment_method,
            $ticket->payment_account_number,
            $ticket->created_at?->toDateTimeString(),
        ];
    }
}
