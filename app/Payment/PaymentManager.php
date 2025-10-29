<?php

namespace App\Payment;

use InvalidArgumentException;

class PaymentManager
{
    protected array $providers = [];
    protected ?string $defaultProvider = null;

    public function __construct()
    {
        $this->defaultProvider = config('payment.default');
    }

    /**
     * Register a payment provider
     */
    public function extend(string $name, PaymentProviderInterface $provider): void
    {
        $this->providers[$name] = $provider;
    }

    /**
     * Get a payment provider instance
     */
    public function provider(?string $name = null): PaymentProviderInterface
    {
        $name = $name ?? $this->defaultProvider;

        if (!isset($this->providers[$name])) {
            throw new InvalidArgumentException("Payment provider [{$name}] not found.");
        }

        return $this->providers[$name];
    }

    /**
     * Initialize payment using specified or default provider
     */
    public function initializePayment(array $data, ?string $provider = null): array
    {
        return $this->provider($provider)->initializePayment($data);
    }

    /**
     * Verify payment using specified or default provider
     */
    public function verifyPayment(string $reference, ?string $provider = null): array
    {
        return $this->provider($provider)->verifyPayment($reference);
    }

    /**
     * Get all registered providers
     */
    public function getProviders(): array
    {
        return array_keys($this->providers);
    }

    /**
     * Dynamically call the default provider instance
     */
    public function __call($method, $parameters)
    {
        return $this->provider()->$method(...$parameters);
    }
}
