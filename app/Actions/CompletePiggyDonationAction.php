<?php

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Events\PiggyDonationProcessed;
use App\Models\PiggyBox;
use App\Models\PiggyDonation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompletePiggyDonationAction
{
    public function execute(PiggyDonation $donation, array $paymentData = [], string $source = 'system'): PiggyDonation
    {
        $dispatchReceipt = false;

        $donation = DB::transaction(function () use ($donation, $paymentData, $source, &$dispatchReceipt) {
            $donation = PiggyDonation::query()
                ->with('piggyBox')
                ->lockForUpdate()
                ->findOrFail($donation->id);

            $paidAmount = $paymentData['amount'] ?? null;
            $amountMatches = $donation->matchesPaidAmount($paidAmount);
            $metadata = array_merge($donation->payment_metadata ?? [], [
                $source => array_filter([
                    'at' => now()->toDateTimeString(),
                    'status' => $paymentData['status'] ?? null,
                    'success' => $paymentData['success'] ?? null,
                    'amount' => $paidAmount,
                    'raw_data' => $paymentData['raw_data'] ?? null,
                    'amount_mismatch' => ! $amountMatches,
                ], fn ($value) => $value !== null),
            ]);

            if (! $amountMatches) {
                Log::warning('PiggyWallet donation amount mismatch', [
                    'donation_id' => $donation->id,
                    'reference' => $donation->payment_reference,
                    'expected_amount' => (float) $donation->amount,
                    'verified_amount' => (float) $paidAmount,
                    'source' => $source,
                ]);

                $donation->forceFill([
                    'payment_status' => PaymentStatus::Failed,
                    'transaction_rrn' => $paymentData['transaction_rrn'] ?? $donation->transaction_rrn,
                    'payment_metadata' => $metadata,
                ])->save();

                return $donation;
            }

            $wasCompleted = $donation->payment_status === PaymentStatus::Completed;
            $wasCredited = filled($donation->credited_at);

            $donation->forceFill([
                'payment_status' => PaymentStatus::Completed,
                'transaction_rrn' => $paymentData['transaction_rrn'] ?? $donation->transaction_rrn,
                'payment_metadata' => $metadata,
                'credited_at' => $donation->credited_at ?? now(),
            ])->save();

            if (! $wasCredited) {
                PiggyBox::query()
                    ->whereKey($donation->piggy_box_id)
                    ->incrementEach([
                        'total_received' => (float) $donation->amount,
                        'donation_count' => 1,
                    ]);
            }

            $dispatchReceipt = ! $wasCompleted && $donation->canEmailReceipt();

            return $donation->fresh('piggyBox');
        });

        if ($dispatchReceipt && $donation->piggyBox) {
            event(new PiggyDonationProcessed($donation, $donation->piggyBox));
        }

        return $donation;
    }
}
