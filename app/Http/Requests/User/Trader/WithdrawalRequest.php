<?php

namespace App\Http\Requests\User\Trader;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalRequest extends Request
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
        $request = [
            'amount' => 'required|numeric|between:0.00000001, 99999999999.99999999',
            'address' => 'required|max:255',
            'stock_item_type' => 'required|in:' . array_to_string(stock_item_types()),
            'accept_policy' => 'required|in:1',
        ];

        if ($this->stock_item_type == CURRENCY_REAL) {
            $request['amount'] = 'required|numeric|between:0.01, 99999999999.99';
            $request['address'] = 'required|email';
        }

        if (env('APP_ENV') != 'local' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE) {
            $request['g-recaptcha-response'] = 'required|captcha';
        }

        return $request;
    }

    /**
     * Purpose: returns custom validation error messages.
     *
     * Action: shows clearer error text for specific form or API validation rules.
     *
     */
    public function messages(): array
    {
        return [
            'stock_item_type' => __('Invalid withdrawal request.'),
        ];
    }

    /**
     * Purpose: returns human-readable names for validated fields.
     *
     * Action: helps the validator display user-friendly field names in error messages.
     *
     */
    public function attributes(): array
    {
        return [
            'accept_policy' => __('The withdrawal policy checking'),
            'g-recaptcha-response' => 'google captcha'
        ];
    }
}
