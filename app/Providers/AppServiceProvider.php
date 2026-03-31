<?php

namespace App\Providers;

use Domain\Payments\Braintree\BraintreeService;
use Domain\Payments\Braintree\HttpBraintreeGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(BraintreeService::class, new HttpBraintreeGateway);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
