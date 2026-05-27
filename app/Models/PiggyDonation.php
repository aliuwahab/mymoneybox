<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PiggyDonation extends Model
{
    protected $fillable = [
        'piggy_box_id',
        'donor_name',
        'donor_email',
        'donor_phone',
        'amount',
        'currency_code',
        'is_anonymous',
        'message',
        'payment_provider',
        'payment_method',
        'payment_reference',
        'payment_status',
        'transaction_rrn',
        'payment_metadata',
        'credited_at',
        'receipt_sent_at',
        'receipt_resent_at',
        'receipt_resend_count',
        'manual_verified_at',
        'manual_verified_by',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'payment_status' => PaymentStatus::class,
        'payment_metadata' => 'array',
        'credited_at' => 'datetime',
        'receipt_sent_at' => 'datetime',
        'receipt_resent_at' => 'datetime',
        'manual_verified_at' => 'datetime',
    ];

    public function piggyBox(): BelongsTo
    {
        return $this->belongsTo(PiggyBox::class);
    }

    public function manualVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manual_verified_by');
    }

    public function getDisplayName(): string
    {
        return $this->is_anonymous ? 'Anonymous' : ($this->donor_name ?? 'Anonymous');
    }

    public function canEmailReceipt(): bool
    {
        return filled($this->donor_email)
            && ! in_array($this->donor_email, ['noreply@mymoneybox.com', 'noreply@mypiggybox.com'], true);
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
