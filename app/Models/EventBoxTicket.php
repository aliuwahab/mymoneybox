<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBoxTicket extends Model
{
    protected $fillable = [
        'event_box_id',
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'amount',
        'payment_reference',
        'payment_status',
        'code',
        'status',
        'redeemed_at',
        'redeemed_by',
        'payment_metadata',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'payment_status'   => PaymentStatus::class,
        'status'           => TicketStatus::class,
        'payment_metadata' => 'array',
        'redeemed_at'      => 'datetime',
    ];

    // Relationships

    public function eventBox(): BelongsTo
    {
        return $this->belongsTo(EventBox::class);
    }

    public function redeemedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }

    // Methods

    public static function generateCode(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no O,0,1,I for readability

        do {
            $segments = [];
            for ($i = 0; $i < 3; $i++) {
                $segment = '';
                for ($j = 0; $j < 4; $j++) {
                    $segment .= $chars[random_int(0, strlen($chars) - 1)];
                }
                $segments[] = $segment;
            }
            $code = 'TKT-' . implode('-', $segments);
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function isRedeemable(): bool
    {
        return $this->payment_status === PaymentStatus::Completed
            && $this->status === TicketStatus::Unused;
    }

    public function redeem(int $userId): void
    {
        $this->update([
            'status'      => TicketStatus::Redeemed,
            'redeemed_at' => now(),
            'redeemed_by' => $userId,
        ]);
    }

    // Scopes

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', PaymentStatus::Completed->value);
    }
}