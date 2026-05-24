<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Unused   = 'unused';
    case Redeemed = 'redeemed';
    case Voided   = 'voided';

    public function label(): string
    {
        return match($this) {
            self::Unused   => 'Unused',
            self::Redeemed => 'Redeemed',
            self::Voided   => 'Voided',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Unused   => 'pill-ok',
            self::Redeemed => 'pill-info',
            self::Voided   => 'pill-danger',
        };
    }
}