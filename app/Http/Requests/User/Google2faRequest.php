<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class Google2faRequest extends Request
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
        $rules = [
            'google_app_code' => 'required|numeric',
        ];

        if ($this->isMethod('PUT')) {
            $rules['password'] = 'required|hash_check:' . Auth::user()->password;
            $rules['back_up'] = 'required|in:1';
        }

        return $rules;
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

    /**
     * Purpose: returns human-readable names for validated fields.
     *
     * Action: helps the validator display user-friendly field names in error messages.
     *
     */
    public function attributes(): array
    {
        return [
            'google_app_code' => __('G2FA'),
            'back_up' => __('Checking'),
        ];
    }
}