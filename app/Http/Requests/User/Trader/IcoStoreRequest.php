<?php

namespace App\Http\Requests\User\Trader;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class IcoStoreRequest extends FormRequest
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
        $minIcoAmountBuy = admin_settings('min_ico_amount_buy');

        return [
            'stock_pair_id' => 'required|integer',
            'amount' => 'required|numeric|between:' . $minIcoAmountBuy . ',99999999999.99999999',
        ];
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
            'stock_pair_id.required' => $errorMessage,
            'stock_pair_id.integer' => $errorMessage
        ];
    }
}
