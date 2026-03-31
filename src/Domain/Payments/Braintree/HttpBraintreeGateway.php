<?php

declare(strict_types=1);

namespace Domain\Payments\Braintree;

class HttpBraintreeGateway implements BraintreeService
{
    public function __construct(
        // protected Gateway $client
    ) {}

    public static function make(): static
    {
        return app(static::class);
    }

    public static function fake(): BraintreeService
    {
        $inMemoryService = new InMemoryBraintreeGateway;

        app()->instance(BraintreeService::class, $inMemoryService);

        return $inMemoryService;
    }

    /**
     * Ideally, you'd have a type for payment here
     */
    public function createPayment(array $payment): void
    {
        // $this->client->transaction()->sale($payment);
    }

    public function submitTransactionForSettlement(string $orderId): void
    {
        // TODO: Implement submitTransactionForSettlement() method.
    }

    public function voidTransaction(string $orderId): void
    {
        // TODO: Implement voidTransaction() method.
    }
}
