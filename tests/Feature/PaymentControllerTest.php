<?php

declare(strict_types=1);

namespace Tests\Feature;

use Domain\Payments\Braintree\BraintreeService;
use Domain\Payments\Braintree\HttpBraintreeGateway;
use Domain\Payments\Braintree\InMemoryBraintreeGateway;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    /**
     * In testing, both mocks and fakes are used to replace real implementations of dependencies, but they serve
     * different purposes and are used in different contexts.
     *
     * Mock: A mock is an object that is used to verify that certain interactions occur.
     * It is typically used to check that a method is called with specific parameters. Mocks are often used in unit
     * tests to ensure that the code under test interacts correctly with its dependencies.
     *
     * Fake: A fake is a simpler implementation of a dependency that is used to make the test run faster or more reliably.
     * Fakes are often used in integration tests to replace complex or slow dependencies with simpler versions that
     * behave in a predictable way.
     */
    #[Test]
    public function can_create_payment(): void
    {
        /**
         * This test would normally be quite slow, as we are invoking the real Braintree Http API.
         */
        $response = $this->json('POST', '/api/payments', [
            'order_id' => 12345,
            'payment_method_token' => 'tok_visa',
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function can_create_a_payment_via_mock(): void
    {
        // A Mock is an example of a test double.
        // This test has some downsides. Mocked tests can be hard to refactor.
        // They can be fragile, as they are tightly coupled to the implementation of the class they are testing.

        // AppServiceProvider
        $this->app->instance(BraintreeService::class, new HttpBraintreeGateway);

        $this->mock(BraintreeService::class)
            ->expects('createPayment')
            ->with([
                'order_id' => 12345,
                'payment_method_token' => 'tok_visa',
            ]);

        $response = $this->json('POST', '/api/payments', [
            'order_id' => 12345,
            'payment_method_token' => 'tok_visa',
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_creates_a_payment_using_a_fake_object(): void
    {
        $inMemoryService = new InMemoryBraintreeGateway;
        $this->app->instance(BraintreeService::class, $inMemoryService);

        $response = $this->json('POST', '/api/payments', [
            'order_id' => 12345,
            'payment_method_token' => 'tok_visa',
        ]);

        $response->assertStatus(200);

        $inMemoryService->assertPaymentReceived([
            'order_id' => 12345,
            'payment_method_token' => 'tok_visa',
        ]);
    }

    #[Test]
    public function it_creates_a_payment_via_fake_object_with_more_elegance(): void
    {
        $inMemoryService = HttpBraintreeGateway::fake();

        $response = $this->json('POST', '/api/payments', [
            'order_id' => 12345,
            'payment_method_token' => 'tok_visa',
        ]);

        $response->assertStatus(200);

        $inMemoryService->assertPaymentReceived([
            'order_id' => 12345,
            'payment_method_token' => 'tok_visa',
        ]);
    }

    #[Test]
    public function ensure_braintree_service_has_correct_default_implementation(): void
    {
        $this->assertInstanceOf(HttpBraintreeGateway::class, $this->app->make(HttpBraintreeGateway::class));
    }

    #[Test]
    public function will_swap_the_default_implementation(): void
    {
        $this->assertInstanceOf(InMemoryBraintreeGateway::class, HttpBraintreeGateway::fake());
    }
}
