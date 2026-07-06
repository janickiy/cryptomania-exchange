<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Request;

class RegisterRequest extends Request
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
        $validation = [
            "first_name" => "required|alpha_space|between:2,255",
            "last_name" => "required|alpha_space|between:2,255",
            "email" => "required|email|unique:users,email|between:5,255",
            "username" => "required|unique:users,username|max:255",
            'password' => 'required|between:6,32|same:password_confirmation',
            'password_confirmation' => 'required',
            "check_agreement" => "required|in:1",
        ];

        if (env('APP_ENV') != 'local' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE) {
            $validation['g-recaptcha-response'] = 'required|captcha';
        }

        return $validation;
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
