<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Enums\MobileMoneyNetwork;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WithdrawalAccount extends Model
{
    protected $fillable = [
        'user_id',
        'account_type',
        'account_name',
        'account_number',
        'mobile_network',
        'bank_name',
        'bank_branch',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'account_type' => AccountType::class,
        'mobile_network' => MobileMoneyNetwork::class,
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moneyBoxWithdrawals(): HasMany
    {
        return $this->hasMany(MoneyBoxWithdrawal::class);
    }

    public function piggyBoxWithdrawals(): HasMany
    {
        return $this->hasMany(PiggyBoxWithdrawal::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Helper Methods
    public function getDisplayName(): string
    {
        if ($this->account_type === AccountType::MobileMoney) {
            return $this->mobile_network->label() . ' - ' . $this->maskAccountNumber();
        }

        return $this->bank_name . ' - ' . $this->maskAccountNumber();
    }

    public function maskAccountNumber(): string
    {
        $number = $this->account_number;
        $length = strlen($number);
        
        if ($length <= 4) {
            return $number;
        }

        return str_repeat('*', $length - 4) . substr($number, -4);
    }

    public function getFullDetails(): string
    {
        if ($this->account_type === AccountType::MobileMoney) {
            return sprintf(
                "%s\n%s\n%s",
                $this->mobile_network->label(),
                $this->account_number,
                $this->account_name
            );
        }

        return sprintf(
            "%s\nAccount: %s\n%s%s",
            $this->bank_name,
            $this->account_number,
            $this->account_name,
            $this->bank_branch ? "\nBranch: {$this->bank_branch}" : ''
        );
    }

    // Set as default account (unset others)
    public function setAsDefault(): void
    {
        // Unset other defaults for this user
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}
