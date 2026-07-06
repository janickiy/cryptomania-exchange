<?php

namespace App\Http\Requests\User\Trader;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StockOrderRequest extends Request
{
    /**
     * Purpose: determines whether the current user may submit this request.
     *
     * Action: returns the access check result before Laravel runs the validation rules.
     *
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Purpose: returns validation rules for incoming request data.
     *
     * Action: keeps request validation out of controllers and lets Laravel validate the payload consistently.
     *
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $stockOrder = [
            'exchange_type' => 'required|in:' . array_to_string(exchange_type()),
            'category' => 'required|in:' . array_to_string(category_type()),
            'stock_pair_id' => 'required|integer',
            'price' => 'required|numeric|between:0.00000001,99999999999.99999999',
            'amount' => 'required|numeric|between:0.00000001,99999999999.99999999',
        ];

        if ($this->has('stop_limit')) {
            $stockOrder['stop_limit'] = 'required|numeric|between:0.00000001,99999999999.99999999';
        }

        return $stockOrder;
    }

    /**
     * Purpose: returns custom validation error messages.
     *
     * Action: shows clearer error text for specific form or API validation rules.
     *
     */
    public function messages(): array
    {
        $errorMessage = __('Invalid Request.');

        return [
            'exchange_type.required' => $errorMessage,
            'exchange_type.in' => $errorMessage,
            'category.required' => $errorMessage,
            'category.in' => $errorMessage,
            'stock_pair_id.required' => $errorMessage,
            'stock_pair_id.integer' => $errorMessage
        ];
    }
}
