<?php

namespace App\Enums;

enum AmountType: string
{
    case Fixed = 'fixed';
    case Variable = 'variable';
    case Minimum = 'minimum';
    case Maximum = 'maximum';
    case Range = 'range';
}
