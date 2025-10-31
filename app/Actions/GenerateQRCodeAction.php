<?php

namespace App\Actions;

use App\Events\QRCodeGenerated;
use App\Models\MoneyBox;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class GenerateQRCodeAction
{
    public function execute(MoneyBox $moneyBox): string
    {
        $url = route('box.show', $moneyBox->slug);

        // Create QR code
        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        // Write QR code to PNG
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $filename = "qr-codes/{$moneyBox->slug}.png";

        // Store on S3 instead of local public disk
        Storage::disk('s3')->put($filename, $result->getString(), 'public');

        event(new QRCodeGenerated($moneyBox, $filename));

        return $filename;
    }
}
