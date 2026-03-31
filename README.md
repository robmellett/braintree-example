## Mocks and Fakes

 In testing, both mocks and fakes are used to replace real implementations of dependencies, but they serve
 different purposes and are used in different contexts.

 Mock: A mock is an object that is used to verify that certain interactions occur.
 It is typically used to check that a method is called with specific parameters. Mocks are often used in unit
 tests to ensure that the code under test interacts correctly with its dependencies.

 Fake: A fake is a simpler implementation of a dependency that is used to make the test run faster or more reliably.
 Fakes are often used in integration tests to replace complex or slow dependencies with simpler versions that
 behave in a predictable way.
 

## Invoking the real Braintree Http API

Normally you'd write a test like this, and it provides a level of confidence that the Braintree Http API is working as expected. 

The downside is that it would be quite slow as it used a real network request.

```php
#[Test]
public function can_create_payment(): void
{
    $response = $this->json('POST', '/api/payments', [
        'order_id' => 12345,
        'payment_method_token' => 'tok_visa',
    ]);

    $response->assertStatus(200);
}
```

## Creating a mock for the Braintree Http API

The next step is to create a mock for the Braintree Http API.

A Mock is an example of a test double.

This test has some downsides. Mocked tests can be hard to refactor.

They can be fragile, as they are tightly coupled to the implementation of the class they are testing.

```php
#[Test]
public function can_create_a_payment_via_mock(): void
{
    // You'd probably have a binding in your AppServiceProvider like this:
    // App\Providers\AppServiceProvider::register()
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
```

## Using a fake object for the Braintree Http API

We can use a fake object to replace the real Braintree Http API at runtime.

While this fake takes a bit more work to set up, the effort is probably worth it in the long run.

Having a `assertPaymentReceived` method on the fake object allows us to verify that the payment was received.

```php
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
```

## Using a Fake Facade for a little more laravel magic

```php
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
```

We can add a couple more assertions to ensure the bindings work as expected.

```php
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
```

You can find the full code for this example in the [`tests`](tests/Feature/PaymentControllerTest.php) directory.

