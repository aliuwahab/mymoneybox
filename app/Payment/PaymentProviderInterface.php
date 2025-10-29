<?php

namespace App\Payment;

use App\Models\Contribution;

interface PaymentProviderInterface
{
    /**
     * Initialize payment and return payment URL
     */
    public function initializePayment(array $data): array;

    /**
     * Verify payment and return status
     */
    public function verifyPayment(string $reference): array;

    /**
     * Handle webhook callback
     */
    public function handleWebhook(array $payload): void;

    /**
     * Get provider name
     */
    public function getName(): string;
}
