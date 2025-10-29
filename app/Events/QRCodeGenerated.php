<?php

namespace App\Events;

use App\Models\MoneyBox;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QRCodeGenerated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public MoneyBox $moneyBox,
        public string $qrCodePath
    ) {}
}
