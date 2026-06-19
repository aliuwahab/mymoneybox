<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contribution extends Model
{
    protected $fillable = [
        'money_box_id',
        'contributor_name',
        'contributor_email',
        'contributor_phone',
        'amount',
        'currency_code',
        'is_anonymous',
        'message',
        'payment_provider',
        'payment_method',
        'payment_reference',
        'payment_status',
        'ip_address',
        'user_agent',
        'transaction_rrn',
        'payment_metadata',
        'webhook_attempts',
        'webhook_last_received_at',
        'webhook_last_status',
        'webhook_last_signature_valid',
        'webhook_last_event_hash',
        'receipt_sent_at',
        'receipt_resent_at',
        'receipt_resend_count',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'payment_status' => PaymentStatus::class,
        'payment_metadata' => 'array',
        'webhook_last_received_at' => 'datetime',
        'webhook_last_signature_valid' => 'boolean',
        'receipt_sent_at' => 'datetime',
        'receipt_resent_at' => 'datetime',
    ];

    public function moneyBox(): BelongsTo
    {
        return $this->belongsTo(MoneyBox::class);
    }

    public function getDisplayName(): string
    {
        return $this->is_anonymous ? 'Anonymous' : ($this->contributor_name ?? 'Anonymous');
    }

    public function getPaymentMethodLabel(): string
    {
        return match($this->payment_method) {
            'MTN'    => 'MTN MoMo',
            'VDF'    => 'Vodafone Cash',
            'ATL'    => 'AirtelTigo',
            'FLT'    => 'Card',
            'GHipss' => 'GHIPSS',
            default  => $this->payment_method ?? 'MoMo',
        };
    }

    public function matchesPaidAmount(mixed $paidAmount): bool
    {
        if ($paidAmount === null || (float) $paidAmount <= 0) {
            return true;
        }

        return (int) round((float) $this->amount * 100) === (int) round((float) $paidAmount * 100);
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', PaymentStatus::Completed);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
