<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventBoxTicketType extends Model
{
    protected $fillable = [
        'event_box_id',
        'name',
        'description',
        'price',
        'capacity',
        'sold',
        'sort_order',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'capacity'   => 'integer',
        'sold'       => 'integer',
        'sort_order' => 'integer',
    ];

    public function eventBox(): BelongsTo
    {
        return $this->belongsTo(EventBox::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(EventBoxTicket::class, 'ticket_type_id');
    }

    public function isAvailable(): bool
    {
        return $this->capacity === null || $this->sold < $this->capacity;
    }

    public function availableCount(): ?int
    {
        return $this->capacity === null ? null : max(0, $this->capacity - $this->sold);
    }

    public function formatPrice(): string
    {
        return 'GH₵ ' . number_format((float) $this->price, 2);
    }
}
