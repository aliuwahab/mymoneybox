<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MoneyBoxWithdrawal extends Model
{
    protected $fillable = [
        'money_box_id',
        'user_id',
        'withdrawal_account_id',
        'amount',
        'fee',
        'net_amount',
        'currency_code',
        'status',
        'reference',
        'payment_provider',
        'transaction_reference',
        'payment_metadata',
        'user_note',
        'rejection_reason',
        'failure_reason',
        'processed_by',
        'processed_at',
        'disbursed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'status' => WithdrawalStatus::class,
        'payment_metadata' => 'array',
        'processed_at' => 'datetime',
        'disbursed_at' => 'datetime',
    ];

    // Relationships
    public function moneyBox(): BelongsTo
    {
        return $this->belongsTo(MoneyBox::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function withdrawalAccount(): BelongsTo
    {
        return $this->belongsTo(WithdrawalAccount::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(WithdrawalNote::class, 'withdrawal');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', WithdrawalStatus::Pending);
    }

    public function scopeInReview($query)
    {
        return $query->where('status', WithdrawalStatus::InReview);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', WithdrawalStatus::Approved);
    }

    public function scopeDisbursed($query)
    {
        return $query->where('status', WithdrawalStatus::Disbursed);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', WithdrawalStatus::Rejected);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', WithdrawalStatus::Failed);
    }

    public function scopeAwaitingDisbursement($query)
    {
        return $query->where('status', WithdrawalStatus::Approved)
            ->whereNull('disbursed_at');
    }

    // Helper Methods
    public function canBeModified(): bool
    {
        return $this->status->canBeModified();
    }

    public function canBeApproved(): bool
    {
        return $this->status->canBeApproved();
    }

    public function canBeRejected(): bool
    {
        return $this->status->canBeRejected();
    }

    public function canBeDisbursed(): bool
    {
        return $this->status->canBeDisbursed();
    }

    public function formatAmount(): string
    {
        return $this->moneyBox->formatAmount($this->amount);
    }

    public function formatNetAmount(): string
    {
        return $this->moneyBox->formatAmount($this->net_amount);
    }

    public function formatFee(): string
    {
        return $this->moneyBox->formatAmount($this->fee);
    }

    // Generate unique reference
    public static function generateReference(): string
    {
        return 'MB-WD-' . strtoupper(uniqid());
    }
}
