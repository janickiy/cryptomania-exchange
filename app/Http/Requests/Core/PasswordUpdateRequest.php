<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class PasswordUpdateRequest extends Request
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
        return [
            'password' => 'required|hash_check:' . Auth::user()->password,
            'new_password' => 'required|confirmed|between:6,32',
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
        return [
            'password.hash_check' => __('Current password is wrong.')
        ];
    }
}
