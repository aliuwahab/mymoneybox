<?php

namespace App\Events;

use App\Models\PiggyBox;
use App\Models\PiggyDonation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PiggyDonationProcessed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public PiggyDonation $donation,
        public PiggyBox $piggyBox,
    ) {}
}
