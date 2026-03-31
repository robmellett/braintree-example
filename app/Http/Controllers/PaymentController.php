<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use Domain\Payments\Braintree\BraintreeService;

class PaymentController extends Controller
{
    public function __construct(
        private BraintreeService $service,
    ) {}

    public function __invoke(PaymentRequest $request)
    {
        $this->service->createPayment($request->validated());
    }
}
