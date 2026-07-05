<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class PasswordResetRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
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

    public function messages(): array
    {
        return [
            'email.exists' => __('The given email is not registered.')
        ];
    }

    public function attributes(): array
    {
        return ['g-recaptcha-response' => 'google captcha'];
    }
}
