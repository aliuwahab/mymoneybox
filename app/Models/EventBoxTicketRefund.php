<?php

namespace App\Models;

use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBoxTicketRefund extends Model
{
    protected $fillable = [
        'event_box_ticket_id',
        'requested_by',
        'reference',
        'status',
        'gross_amount',
        'charge_amount',
        'refund_amount',
        'currency_code',
        'recipient_account_number',
        'recipient_network',
        'recipient_name',
        'payment_provider',
        'transaction_reference',
        'reason',
        'requested_ip_address',
        'requested_user_agent',
        'payment_metadata',
        'processed_at',
        'completed_at',
        'failed_at',
        'failure_reason',
    ];

    protected $casts = [
        'status' => RefundStatus::class,
        'gross_amount' => 'decimal:2',
        'charge_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'payment_metadata' => 'array',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(EventBoxTicket::class, 'event_box_ticket_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
