<?php

namespace App\Actions;

use App\Events\QRCodeGenerated;
use App\Models\MoneyBox;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class GenerateQRCodeAction
{
    public function execute(MoneyBox $moneyBox): string
    {
        $url = route('box.show', $moneyBox->slug);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->build();

        $filename = "qr-codes/{$moneyBox->slug}.png";

        Storage::disk('public')->put($filename, $result->getString());

        event(new QRCodeGenerated($moneyBox, $filename));

        return $filename;
    }
}
