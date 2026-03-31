<?php

declare(strict_types=1);

namespace Domain\Payments\Braintree;

use Illuminate\Testing\Assert;

class InMemoryBraintreeGateway implements BraintreeService
{
    public array $payments = [];

    public static function make(): static
    {
        return app(static::class);
    }

    public static function fake(): BraintreeService
    {
        $self = new static;

        app()->instance(BraintreeService::class, $self);

        return $self;
    }

    /**
     * Ideally, you'd have a type for payment here
     */
    public function createPayment(array $payment): void
    {
        $this->payments[] = $payment;
    }

    public function assertPaymentReceived(array $payment)
    {
        $hasReceivedPayment = in_array($payment, $this->payments, true);

        Assert::assertTrue($hasReceivedPayment, 'The expected payment address was not received.');
    }
}
