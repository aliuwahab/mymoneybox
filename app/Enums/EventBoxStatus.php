<?php

namespace App\Enums;

enum EventBoxStatus: string
{
    case Draft     = 'draft';
    case Active    = 'active';
    case SoldOut   = 'sold_out';
    case Ended     = 'ended';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft     => 'Draft',
            self::Active    => 'Active',
            self::SoldOut   => 'Sold Out',
            self::Ended     => 'Ended',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft     => 'pill-muted',
            self::Active    => 'pill-ok',
            self::SoldOut   => 'pill-warn',
            self::Ended     => 'pill-muted',
            self::Cancelled => 'pill-danger',
        };
    }
}