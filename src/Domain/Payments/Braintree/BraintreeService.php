<?php

declare(strict_types=1);

namespace Domain\Payments\Braintree;

interface BraintreeService
{
    public static function fake(): BraintreeService;

    public function createPayment(array $payment): void;
}
