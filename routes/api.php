<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/payments', [PaymentController::class, '__invoke'])
    ->name('payment.create');
