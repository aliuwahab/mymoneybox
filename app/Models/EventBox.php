<?php

namespace App\Models;

use App\Enums\EventBoxStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EventBox extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'venue',
        'event_date',
        'ticket_price',
        'capacity',
        'tickets_sold',
        'status',
        'fee_percentage',
    ];

    protected $casts = [
        'event_date'     => 'datetime',
        'ticket_price'   => 'decimal:2',
        'fee_percentage' => 'decimal:2',
        'tickets_sold'   => 'integer',
        'capacity'       => 'integer',
        'status'         => EventBoxStatus::class,
    ];

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(EventBoxTicket::class);
    }

    // Helper methods

    public function isActive(): bool
    {
        return $this->status === EventBoxStatus::Active
            && $this->event_date->isFuture();
    }

    public function isSoldOut(): bool
    {
        return $this->capacity !== null
            && $this->tickets_sold >= $this->capacity;
    }

    public function availableCapacity(): ?int
    {
        if ($this->capacity === null) {
            return null;
        }

        return max(0, $this->capacity - $this->tickets_sold);
    }

    public function canPurchase(): bool
    {
        return $this->isActive() && !$this->isSoldOut();
    }

    public function getPublicUrl(): string
    {
        return route('events.show', $this->slug);
    }

    public function formatPrice(): string
    {
        return 'GH₵ ' . number_format((float) $this->ticket_price, 2);
    }

    public function getCoverImageUrl(): ?string
    {
        $media = $this->getFirstMedia('cover');

        if (!$media) {
            return null;
        }

        try {
            return $media->getTemporaryUrl(now()->addMinutes(30));
        } catch (\Exception $e) {
            return $media->getUrl();
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->useDisk('s3')
            ->singleFile();
    }

    public function getEffectiveFeePercentage(): float
    {
        return (float) ($this->fee_percentage ?? config('withdrawal.fee_percentage', 1.5));
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', EventBoxStatus::Active->value)
            ->where('event_date', '>=', now());
    }
}