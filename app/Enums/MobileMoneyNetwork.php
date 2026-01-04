<?php

namespace App\Enums;

enum MobileMoneyNetwork: string
{
    case MTN = 'mtn';
    case Vodafone = 'vodafone';
    case AirtelTigo = 'airteltigo';

    public function label(): string
    {
        return match($this) {
            self::MTN => 'MTN Mobile Money',
            self::Vodafone => 'Vodafone Cash',
            self::AirtelTigo => 'AirtelTigo Money',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::MTN => 'yellow',
            self::Vodafone => 'red',
            self::AirtelTigo => 'blue',
        };
    }
}
