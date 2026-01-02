<?php

namespace App\Console\Commands;

use App\Actions\GeneratePiggyQRCodeAction;
use App\Actions\GenerateQRCodeAction;
use App\Models\MoneyBox;
use App\Models\PiggyBox;
use Illuminate\Console\Command;

class RegenerateQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:regenerate {--force : Force regeneration even if QR code exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate QR codes for all money boxes and piggy boxes';

    /**
     * Execute the console command.
     */
    public function handle(
        GenerateQRCodeAction $generateMoneyBoxQR,
        GeneratePiggyQRCodeAction $generatePiggyBoxQR
    ): int
    {
        $force = $this->option('force');

        $this->info('Starting QR code regeneration...');
        $this->newLine();

        // Regenerate Money Box QR codes
        $this->info('Processing Money Boxes...');
        $moneyBoxes = MoneyBox::all();
        $moneyBoxCount = 0;

        foreach ($moneyBoxes as $moneyBox) {
            if ($force && $moneyBox->hasQrCode()) {
                // Delete existing QR code
                $moneyBox->clearMediaCollection('qr_code');
                $this->line("  Deleted old QR code for: {$moneyBox->title}");
            }

            if (!$moneyBox->hasQrCode()) {
                $generateMoneyBoxQR->execute($moneyBox);
                $this->info("  ✓ Generated QR code for: {$moneyBox->title}");
                $moneyBoxCount++;
            } else {
                $this->line("  - Skipped (already exists): {$moneyBox->title}");
            }
        }

        $this->newLine();

        // Regenerate Piggy Box QR codes
        $this->info('Processing Piggy Boxes...');
        $piggyBoxes = PiggyBox::with('user')->get();
        $piggyBoxCount = 0;

        foreach ($piggyBoxes as $piggyBox) {
            if ($force && $piggyBox->hasQrCode()) {
                // Delete existing QR code
                $piggyBox->clearMediaCollection('qr_code');
                $this->line("  Deleted old QR code for: {$piggyBox->user->name}'s piggy box");
            }

            if (!$piggyBox->hasQrCode()) {
                $generatePiggyBoxQR->execute($piggyBox);
                $this->info("  ✓ Generated QR code for: {$piggyBox->user->name}'s piggy box");
                $piggyBoxCount++;
            } else {
                $this->line("  - Skipped (already exists): {$piggyBox->user->name}'s piggy box");
            }
        }

        $this->newLine();
        $this->info("✅ Completed!");
        $this->info("   Money Boxes: {$moneyBoxCount} generated");
        $this->info("   Piggy Boxes: {$piggyBoxCount} generated");

        return Command::SUCCESS;
    }
}
