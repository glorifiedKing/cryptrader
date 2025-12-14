<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supportedSymbols = config('trading.supported_symbols');

        return [
            'symbol' => ['required', 'string', 'in:' . implode(',', $supportedSymbols)],
            'side' => ['required', 'string', 'in:buy,sell'],
            'price' => ['required', 'numeric', 'min:0.00000001'],
            'amount' => ['required', 'numeric', 'min:0.00000001'],
        ];
    }
}
