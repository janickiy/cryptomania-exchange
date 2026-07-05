<?php

namespace App\Http\Requests\User\Trader;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class DepositRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|between:0.01, 99999999999.99',
            'accept_policy' => 'required|in:1',
        ];
    }

    public function attributes(): array
    {
        return [
            'accept_policy' => __('The deposit policy checking'),
        ];
    }
}
