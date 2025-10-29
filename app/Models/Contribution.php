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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'payment_status' => PaymentStatus::class,
    ];

    public function moneyBox(): BelongsTo
    {
        return $this->belongsTo(MoneyBox::class);
    }

    public function getDisplayName(): string
    {
        return $this->is_anonymous ? 'Anonymous' : ($this->contributor_name ?? 'Anonymous');
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
