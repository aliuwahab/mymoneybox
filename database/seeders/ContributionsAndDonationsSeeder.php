<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Models\Contribution;
use App\Models\MoneyBox;
use App\Models\PiggyBox;
use App\Models\PiggyDonation;
use Illuminate\Database\Seeder;

class ContributionsAndDonationsSeeder extends Seeder
{
    public function run(): void
    {
        $this->info('Creating contributions and donations for testing...');

        // Get all money boxes and piggy boxes
        $moneyBoxes = MoneyBox::all();
        $piggyBoxes = PiggyBox::all();

        // Create contributions for each money box
        foreach ($moneyBoxes as $moneyBox) {
            $this->info("Adding contributions to MoneyBox: {$moneyBox->title}");
            
            // Create 15-25 completed contributions
            $contributionCount = rand(15, 25);
            $totalAmount = 0;

            for ($i = 0; $i < $contributionCount; $i++) {
                $amount = rand(10, 500);
                $isAnonymous = rand(0, 10) > 7; // 30% anonymous

                $contribution = Contribution::create([
                    'money_box_id' => $moneyBox->id,
                    'contributor_name' => $isAnonymous ? null : fake()->name(),
                    'contributor_email' => $isAnonymous ? null : fake()->email(),
                    'contributor_phone' => $isAnonymous ? null : fake()->phoneNumber(),
                    'amount' => $amount,
                    'currency_code' => $moneyBox->currency_code,
                    'is_anonymous' => $isAnonymous,
                    'message' => rand(0, 10) > 5 ? fake()->sentence() : null,
                    'payment_provider' => 'trendipay',
                    'payment_method' => collect(['mobile_money', 'card', 'bank_transfer'])->random(),
                    'payment_reference' => 'CONTRIB_' . strtoupper(uniqid()),
                    'payment_status' => PaymentStatus::Completed,
                    'transaction_rrn' => 'RRN' . rand(100000000, 999999999),
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'payment_metadata' => [
                        'provider' => 'trendipay',
                        'completed_at' => now()->subDays(rand(1, 30))->toDateTimeString(),
                    ],
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);

                $totalAmount += $amount;
            }

            // Update money box totals
            $moneyBox->update([
                'total_contributions' => $totalAmount,
                'contribution_count' => $contributionCount,
            ]);

            $this->info("  ✓ Created {$contributionCount} contributions totaling {$moneyBox->getCurrencySymbol()}{$totalAmount}");
        }

        // Create donations for each piggy box
        foreach ($piggyBoxes as $piggyBox) {
            $this->info("Adding donations to PiggyBox for user: {$piggyBox->user->name}");
            
            // Create 10-20 completed donations
            $donationCount = rand(10, 20);
            $totalAmount = 0;

            for ($i = 0; $i < $donationCount; $i++) {
                $amount = rand(5, 300);
                $isAnonymous = rand(0, 10) > 6; // 40% anonymous

                $donation = PiggyDonation::create([
                    'piggy_box_id' => $piggyBox->id,
                    'donor_name' => $isAnonymous ? 'Anonymous' : fake()->name(),
                    'donor_email' => $isAnonymous ? 'anonymous@mymoneybox.com' : fake()->email(),
                    'donor_phone' => $isAnonymous ? null : fake()->phoneNumber(),
                    'amount' => $amount,
                    'currency_code' => $piggyBox->currency_code,
                    'is_anonymous' => $isAnonymous,
                    'message' => rand(0, 10) > 6 ? fake()->sentence() : null,
                    'payment_provider' => 'trendipay',
                    'payment_reference' => 'PIGGY_' . strtoupper(uniqid()),
                    'payment_status' => PaymentStatus::Completed,
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);

                $totalAmount += $amount;
            }

            // Update piggy box totals
            $piggyBox->update([
                'total_received' => $totalAmount,
                'donation_count' => $donationCount,
            ]);

            $this->info("  ✓ Created {$donationCount} donations totaling {$piggyBox->getCurrencySymbol()}{$totalAmount}");
        }

        $this->info('');
        $this->info('✅ Seeding completed!');
        $this->info('');
        $this->info('Summary:');
        $this->info("  - MoneyBoxes with contributions: {$moneyBoxes->count()}");
        $this->info("  - PiggyBoxes with donations: {$piggyBoxes->count()}");
        $this->info('  - Total contributions: ' . Contribution::where('payment_status', PaymentStatus::Completed)->count());
        $this->info('  - Total donations: ' . PiggyDonation::where('payment_status', PaymentStatus::Completed)->count());
        $this->info('');
        $this->info('You can now test withdrawals! Available balances:');
        
        foreach ($moneyBoxes as $box) {
            $available = $box->getAvailableBalance();
            $this->info("  - {$box->title}: {$box->getCurrencySymbol()}{$available}");
        }
        
        foreach ($piggyBoxes as $box) {
            $available = $box->getAvailableBalance();
            $this->info("  - Piggy Box ({$box->user->name}): {$box->getCurrencySymbol()}{$available}");
        }
    }

    protected function info(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
