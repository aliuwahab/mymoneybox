<?php

namespace App\Listeners;

use App\Events\PiggyDonationProcessed;
use App\Mail\PiggyDonationReceiptMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPiggyDonationReceiptEmail implements ShouldQueue
{
    public function handle(PiggyDonationProcessed $event): void
    {
        $donation = $event->donation;

        if (! $donation->canEmailReceipt()) {
            return;
        }

        try {
            Mail::to($donation->donor_email)
                ->send(new PiggyDonationReceiptMail($donation, $event->piggyBox));

            $donation->forceFill([
                'receipt_sent_at' => $donation->receipt_sent_at ?? now(),
            ])->save();
        } catch (\Throwable $e) {
            Log::error('Failed to send Piggy Wallet donation receipt', [
                'donation_id' => $donation->id,
                'email' => $donation->donor_email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
