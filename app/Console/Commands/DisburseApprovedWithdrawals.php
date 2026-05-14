<?php

namespace App\Console\Commands;

use App\Actions\DisburseWithdrawalAction;
use App\Models\MoneyBoxWithdrawal;
use App\Models\PiggyBoxWithdrawal;
use Illuminate\Console\Command;

class DisburseApprovedWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'withdrawals:disburse {--force : Force disburse without 12-hour wait}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disburse approved withdrawals that are more than 12 hours old';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting withdrawal disbursement...');

        $force = $this->option('force');
        $twelveHoursAgo = now()->subHours(12);

        // Process MoneyBox withdrawals
        $moneyBoxQuery = MoneyBoxWithdrawal::with(['moneyBox', 'withdrawalAccount', 'user'])
            ->where('status', 'approved')
            ->whereNull('disbursed_at');

        if (!$force) {
            $moneyBoxQuery->where('processed_at', '<=', $twelveHoursAgo);
        }

        $moneyBoxWithdrawals = $moneyBoxQuery->get();
        $this->info("Found {$moneyBoxWithdrawals->count()} MoneyBox withdrawals to disburse");

        // Process PiggyBox withdrawals
        $piggyBoxQuery = PiggyBoxWithdrawal::with(['piggyBox', 'withdrawalAccount', 'user'])
            ->where('status', 'approved')
            ->whereNull('disbursed_at');

        if (!$force) {
            $piggyBoxQuery->where('processed_at', '<=', $twelveHoursAgo);
        }

        $piggyBoxWithdrawals = $piggyBoxQuery->get();
        $this->info("Found {$piggyBoxWithdrawals->count()} PiggyBox withdrawals to disburse");

        $successCount = 0;
        $failedCount = 0;

        $action = app(DisburseWithdrawalAction::class);

        // Process MoneyBox withdrawals
        foreach ($moneyBoxWithdrawals as $withdrawal) {
            $this->line("Processing {$withdrawal->reference} - {$withdrawal->currency_code} {$withdrawal->net_amount}");
            $result = $action->execute($withdrawal);
            if ($result['success']) {
                $this->info("✓ Submitted {$withdrawal->reference}");
                $successCount++;
            } else {
                $this->error("✗ Failed {$withdrawal->reference}: " . ($result['message'] ?? ''));
                $failedCount++;
            }
        }

        // Process PiggyBox withdrawals
        foreach ($piggyBoxWithdrawals as $withdrawal) {
            $this->line("Processing {$withdrawal->reference} - {$withdrawal->currency_code} {$withdrawal->net_amount}");
            $result = $action->execute($withdrawal);
            if ($result['success']) {
                $this->info("✓ Submitted {$withdrawal->reference}");
                $successCount++;
            } else {
                $this->error("✗ Failed {$withdrawal->reference}: " . ($result['message'] ?? ''));
                $failedCount++;
            }
        }

        $this->info("\n=== Disbursement Summary ===");
        $this->info("Successful: {$successCount}");
        $this->error("Failed: {$failedCount}");
        $this->info("Total: " . ($successCount + $failedCount));

        return Command::SUCCESS;
    }
}
