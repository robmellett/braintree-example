<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'order_id' => 'required|integer',
            'payment_method_token' => 'required|string',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
