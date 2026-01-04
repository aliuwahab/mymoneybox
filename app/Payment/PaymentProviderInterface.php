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
     *
     * @return array Processed webhook data with payment status
     */
    public function handleWebhook(array $payload): array;

    /**
     * Transfer money to a recipient
     *
     * @param array $data Transfer data including amount, recipient details, reference
     * @return array Transfer result with success status and transaction details
     */
    public function transferAmount(array $data): array;

    /**
     * Get provider name
     */
    public function getName(): string;
}
