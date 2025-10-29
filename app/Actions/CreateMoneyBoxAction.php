<?php

namespace App\Actions;

use App\Events\MoneyBoxCreated;
use App\Models\MoneyBox;
use Illuminate\Support\Str;

class CreateMoneyBoxAction
{
    public function __construct(
        protected GenerateQRCodeAction $generateQRCodeAction
    ) {}

    public function execute(array $data): MoneyBox
    {
        // Generate unique slug
        $slug = Str::slug($data['title']) . '-' . Str::random(6);

        $moneyBox = MoneyBox::create([
            ...$data,
            'slug' => $slug,
        ]);

        // Generate QR Code
        $qrCodePath = $this->generateQRCodeAction->execute($moneyBox);
        $moneyBox->update(['qr_code_path' => $qrCodePath]);

        event(new MoneyBoxCreated($moneyBox));

        return $moneyBox;
    }
}
