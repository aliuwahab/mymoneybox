<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PiggyBox extends Model implements HasMedia
{
    use InteractsWithMedia;
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

    /**
     * Get QR code URL
     */
    public function getQrCodeUrl(): ?string
    {
        $media = $this->getFirstMedia('qr_code');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Check if QR code exists
     */
    public function hasQrCode(): bool
    {
        return $this->hasMedia('qr_code');
    }

    /**
     * Get public donation URL
     */
    public function getPublicUrl(): string
    {
        return route('piggy.show', $this->user->piggy_code);
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('qr_code')
            ->singleFile(); // Only one QR code
    }
}
