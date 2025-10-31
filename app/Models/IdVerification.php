<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class IdVerification extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'id_type',
        'first_name',
        'last_name',
        'other_names',
        'id_number',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
        'expires_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->isApproved() && !$this->isExpired();
    }

    public function getFrontImageUrl(): ?string
    {
        $media = $this->getFirstMedia('front');
        return $media ? $media->getTemporaryUrl(now()->addHour()) : null;
    }

    public function getBackImageUrl(): ?string
    {
        $media = $this->getFirstMedia('back');
        return $media ? $media->getTemporaryUrl(now()->addHour()) : null;
    }

    public function getIdTypeLabel(): string
    {
        return match($this->id_type) {
            'passport' => 'Passport',
            'national_card' => 'Ghana Card',
            'drivers_license' => 'Driver\'s License',
            default => $this->id_type,
        };
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('front')
            ->useDisk('s3')
            ->singleFile();

        $this->addMediaCollection('back')
            ->useDisk('s3')
            ->singleFile();
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('status', 'approved')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
