<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'country_id',
        'piggy_code',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_type' => UserType::class,
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type == UserType::Admin;
    }

    /**
     * Check if user is a regular user
     */
    public function isUser(): bool
    {
        return $this->user_type === UserType::User;
    }

    /**
     * Get the user's country
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get all money boxes (for causes) created by the user
     */
    public function moneyBoxes(): HasMany
    {
        return $this->hasMany(MoneyBox::class);
    }

    /**
     * Get the user's personal piggy box
     */
    public function piggyBox(): HasOne
    {
        return $this->hasOne(PiggyBox::class);
    }

    /**
     * Get all ID verifications for the user
     */
    public function idVerifications(): HasMany
    {
        return $this->hasMany(IdVerification::class);
    }

    /**
     * Get the user's current valid verification
     */
    public function currentVerification(): HasOne
    {
        return $this->hasOne(IdVerification::class)
            ->where('status', 'approved')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->latestOfMany();
    }

    /**
     * Check if user is verified
     */
    public function isVerified(): bool
    {
        return $this->currentVerification()->exists();
    }

    /**
     * Generate a unique piggy code for the user
     */
    public static function generateUniquePiggyCode(): string
    {
        do {
            $code = strtoupper(Str::random(4) . rand(0, 9));
        } while (self::where('piggy_code', $code)->exists());

        return $code;
    }
}
