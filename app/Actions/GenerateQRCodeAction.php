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
    public function execute(MoneyBox $moneyBox): void
    {
        // Check if QR code already exists, reuse it
        if ($moneyBox->hasQrCode()) {
            return;
        }

        $url = route('box.show', $moneyBox->slug);

        // Create QR code
        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 400,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        // Write QR code to PNG
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Create temporary file
        $tempPath = tempnam(sys_get_temp_dir(), 'qr_');
        file_put_contents($tempPath, $result->getString());

        // Add to media collection using Spatie Media Library
        $moneyBox->addMedia($tempPath)
            ->usingFileName("qr-code-{$moneyBox->slug}.png")
            ->usingName("QR Code for {$moneyBox->title}")
            ->toMediaCollection('qr_code');

        // Clean up temp file
        @unlink($tempPath);

        event(new QRCodeGenerated($moneyBox));
    }
}
