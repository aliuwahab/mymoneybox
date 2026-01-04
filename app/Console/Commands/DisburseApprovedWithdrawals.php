<?php

namespace App\Console\Commands;

use App\Models\MoneyBoxWithdrawal;
use App\Models\PiggyBoxWithdrawal;
use App\Payment\PaymentManager;
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

        // Process MoneyBox withdrawals
        foreach ($moneyBoxWithdrawals as $withdrawal) {
            $result = $this->disburseWithdrawal($withdrawal, 'money-box');
            if ($result) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        // Process PiggyBox withdrawals
        foreach ($piggyBoxWithdrawals as $withdrawal) {
            $result = $this->disburseWithdrawal($withdrawal, 'piggy-box');
            if ($result) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        $this->info("\n=== Disbursement Summary ===");
        $this->info("Successful: {$successCount}");
        $this->error("Failed: {$failedCount}");
        $this->info("Total: " . ($successCount + $failedCount));

        return Command::SUCCESS;
    }

    private function disburseWithdrawal($withdrawal, $type): bool
    {
        try {
            $account = $withdrawal->withdrawalAccount;
            if (!$account) {
                $this->error("Withdrawal {$withdrawal->reference} has no account");
                return false;
            }

            $this->line("Processing {$withdrawal->reference} - {$withdrawal->currency_code} {$withdrawal->net_amount}");

            // Get payment provider
            $paymentManager = app(PaymentManager::class);
            $provider = $paymentManager->provider($withdrawal->payment_provider ?? 'trendipay');

            // Prepare transfer data
            $transferData = [
                'reference' => $withdrawal->reference . '-DISBURSE-' . now()->timestamp,
                'amount' => $withdrawal->net_amount,
                'account_number' => $account->account_number,
                'account_name' => $account->account_name,
                'account_type' => $account->account_type->value,
                'network' => $account->network?->value ?? 'mtn',
                'sender_name' => config('app.name'),
                'description' => "Withdrawal: {$withdrawal->reference}",
            ];

            // Add bank code if bank account
            if ($account->account_type->value === 'bank' && $account->bank_code) {
                $transferData['bank_code'] = $account->bank_code;
            }

            // Execute transfer
            $result = $provider->transferAmount($transferData);

            if ($result['success']) {
                // Update withdrawal as disbursed
                $withdrawal->update([
                    'status' => 'disbursed',
                    'disbursed_at' => now(),
                    'transaction_reference' => $result['transaction_reference'] ?? null,
                    'payment_metadata' => array_merge($withdrawal->payment_metadata ?? [], [
                        'disbursement' => $result,
                        'disbursed_by_command' => true,
                    ]),
                ]);

                $this->info("✓ Disbursed {$withdrawal->reference}");
                return true;
            } else {
                $this->error("✗ Failed to disburse {$withdrawal->reference}: {$result['message']}");

                // Update withdrawal with failure reason
                $withdrawal->update([
                    'failure_reason' => $result['message'] ?? 'Transfer failed',
                    'payment_metadata' => array_merge($withdrawal->payment_metadata ?? [], [
                        'disbursement_attempt' => $result,
                        'attempted_at' => now()->toDateTimeString(),
                    ]),
                ]);

                return false;
            }
        } catch (\Exception $e) {
            $this->error("✗ Exception for {$withdrawal->reference}: {$e->getMessage()}");

            // Update withdrawal with error
            $withdrawal->update([
                'failure_reason' => 'System error: ' . $e->getMessage(),
                'payment_metadata' => array_merge($withdrawal->payment_metadata ?? [], [
                    'error' => $e->getMessage(),
                    'attempted_at' => now()->toDateTimeString(),
                ]),
            ]);

            return false;
        }
    }
}
