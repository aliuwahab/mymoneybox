<?php

namespace App\Actions;

use App\Models\PiggyBox;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class GeneratePiggyQRCodeAction
{
    public function execute(PiggyBox $piggyBox): void
    {
        // Check if QR code already exists, reuse it
        if ($piggyBox->hasQrCode()) {
            return;
        }

        $url = $piggyBox->getPublicUrl();

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
        $tempPath = tempnam(sys_get_temp_dir(), 'piggy_qr_');
        file_put_contents($tempPath, $result->getString());

        // Add to media collection using Spatie Media Library
        $piggyBox->addMedia($tempPath)
            ->usingFileName("qr-code-piggy-{$piggyBox->user->piggy_code}.png")
            ->usingName("QR Code for {$piggyBox->title}")
            ->toMediaCollection('qr_code');

        // Clean up temp file
        @unlink($tempPath);
    }
}
