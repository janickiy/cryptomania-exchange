<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Request;

class NewPasswordRequest extends Request
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
        $validation = [
            'new_password' => 'required|confirmed',
        ];

        if (env('APP_ENV') != 'local' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE) {
            $validation['g-recaptcha-response'] = 'required|captcha';
        }

        return $validation;
    }

    public function attributes(): array
    {
        return ['g-recaptcha-response' => 'google captcha'];
    }
}
