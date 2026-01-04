<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WithdrawalNote extends Model
{
    protected $fillable = [
        'withdrawal_type',
        'withdrawal_id',
        'user_id',
        'note',
        'is_admin',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    // Relationships
    public function withdrawal(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeAdminNotes($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeUserNotes($query)
    {
        return $query->where('is_admin', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
