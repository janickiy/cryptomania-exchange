<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class PasswordResetRequest extends Request
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
        return true;
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
        if (!Auth::user()) {
            $validation = [
                'email' => 'required|email|exists:users,email|between:5,255'
            ];

            if (env('APP_ENV') != 'local' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE) {
                $validation['g-recaptcha-response'] = 'required|captcha';
            }

            return $validation;
        }
        return [];
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
            'email.exists' => __('The given email is not registered.')
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
        return ['g-recaptcha-response' => 'google captcha'];
    }
}
