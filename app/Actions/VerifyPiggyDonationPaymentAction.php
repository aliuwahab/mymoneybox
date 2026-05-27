<?php

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Models\PiggyDonation;
use App\Payment\PaymentManager;

class VerifyPiggyDonationPaymentAction
{
    public function __construct(
        protected PaymentManager $paymentManager,
        protected CompletePiggyDonationAction $completeDonation,
    ) {}

    public function execute(PiggyDonation $donation, ?int $adminUserId = null): PiggyDonation
    {
        $verification = $this->paymentManager->verifyPayment($donation->payment_reference);

        if (($verification['status'] ?? 'failed') === 'completed') {
            $donation = $this->completeDonation->execute($donation, $verification, 'manual_verification');
        } else {
            $donation->forceFill([
                'payment_status' => ($verification['status'] ?? null) === 'pending'
                    ? PaymentStatus::Pending
                    : PaymentStatus::Failed,
                'transaction_rrn' => $verification['transaction_rrn'] ?? $donation->transaction_rrn,
                'payment_metadata' => array_merge($donation->payment_metadata ?? [], [
                    'manual_verification' => [
                        'at' => now()->toDateTimeString(),
                        'status' => $verification['status'] ?? null,
                        'success' => $verification['success'] ?? null,
                        'message' => $verification['message'] ?? null,
                        'raw_data' => $verification['raw_data'] ?? null,
                    ],
                ]),
            ])->save();
        }

        $donation->forceFill([
            'manual_verified_at' => now(),
            'manual_verified_by' => $adminUserId,
        ])->save();

        return $donation->fresh('piggyBox');
    }
}
