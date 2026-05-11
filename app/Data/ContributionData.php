<?php

namespace App\Data;

use App\Enums\PaymentStatus;
use Spatie\LaravelData\Data;

class ContributionData extends Data
{
    public function __construct(
        public readonly float $amount,
        public readonly string $contributorEmail,
        public readonly ?string $contributorName = null,
        public readonly ?string $contributorPhone = null,
        public readonly bool $isAnonymous = false,
        public readonly ?string $message = null,
        public readonly string $paymentProvider = 'trendipay',
        public readonly ?string $paymentMethod = null,
        public readonly ?string $paymentReference = null,
        public readonly PaymentStatus $paymentStatus = PaymentStatus::Pending,
    ) {}
}