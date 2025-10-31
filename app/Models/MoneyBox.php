<?php

namespace App\Models;

use App\Enums\AmountType;
use App\Enums\ContributorIdentity;
use App\Enums\Visibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MoneyBox extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'goal_amount',
        'currency_code',
        'visibility',
        'contributor_identity',
        'amount_type',
        'fixed_amount',
        'minimum_amount',
        'maximum_amount',
        'start_date',
        'end_date',
        'is_ongoing',
        'qr_code_path',
        'total_contributions',
        'contribution_count',
        'is_active',
    ];

    protected $casts = [
        'visibility' => Visibility::class,
        'contributor_identity' => ContributorIdentity::class,
        'amount_type' => AmountType::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_ongoing' => 'boolean',
        'is_active' => 'boolean',
        'goal_amount' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
        'total_contributions' => 'decimal:2',
        'contribution_count' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('visibility', Visibility::Public);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->where('is_ongoing', true)
                  ->orWhere(function($q2) {
                      $q2->whereNull('end_date')
                         ->orWhere('end_date', '>=', now());
                  });
            });
    }

    public function scopeStarted($query)
    {
        return $query->where(function($q) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', now());
        });
    }

    // Helper Methods
    public function isActive(): bool
    {
        if (!$this->is_active) return false;

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if (!$this->is_ongoing && $this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    public function getPublicUrl(): string
    {
        return route('box.show', $this->slug);
    }

    public function getCurrencySymbol(): string
    {
        return Country::where('currency_code', $this->currency_code)
            ->value('currency_symbol') ?? $this->currency_code;
    }

    public function formatAmount($amount): string
    {
        return $this->getCurrencySymbol() . number_format($amount, 2);
    }

    public function canAcceptContributions(): bool
    {
        return $this->isActive();
    }

    public function validateContributionAmount($amount): bool
    {
        switch ($this->amount_type) {
            case AmountType::Fixed:
                return $amount == $this->fixed_amount;

            case AmountType::Minimum:
                return $amount >= $this->minimum_amount;

            case AmountType::Maximum:
                return $amount <= $this->maximum_amount;

            case AmountType::Range:
                return $amount >= $this->minimum_amount
                    && $amount <= $this->maximum_amount;

            case AmountType::Variable:
            default:
                return $amount > 0;
        }
    }

    public function getProgressPercentage(): float
    {
        if (!$this->goal_amount || $this->goal_amount <= 0) {
            return 0;
        }

        return min(100, ($this->total_contributions / $this->goal_amount) * 100);
    }

    public function getMainImageUrl(): ?string
    {
        $media = $this->getFirstMedia('main');
        return $media ? $media->getTemporaryUrl(now()->addHour()) : null;
    }

    public function getGalleryImageUrls(): array
    {
        return $this->getMedia('gallery')->map(function ($media) {
            return $media->getTemporaryUrl(now()->addHour());
        })->toArray();
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main')
            ->useDisk('s3')
            ->singleFile(); // Only one main image

        $this->addMediaCollection('gallery')
            ->useDisk('s3');
    }
}
