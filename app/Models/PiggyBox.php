<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PiggyBox extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'currency_code',
        'total_received',
        'donation_count',
        'is_active',
    ];

    protected $casts = [
        'total_received' => 'decimal:2',
        'donation_count' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(PiggyDonation::class);
    }

    // Helper Methods
    public function getCurrencySymbol(): string
    {
        return Country::where('currency_code', $this->currency_code)
            ->value('currency_symbol') ?? $this->currency_code;
    }

    public function formatAmount($amount): string
    {
        return $this->getCurrencySymbol() . number_format($amount, 2);
    }

    public function canReceiveDonations(): bool
    {
        return $this->is_active;
    }
}
