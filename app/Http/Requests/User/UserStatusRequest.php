<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class UserStatusRequest extends Request
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
        return [
            'is_email_verified' => 'required|in:' . array_to_string(email_status()),
            'is_active' => 'required|in:' . array_to_string(account_status()),
            'is_financial_active' => 'required|in:' . array_to_string(financial_status()),
            'is_accessible_under_maintenance' => 'required|in:' . array_to_string(maintenance_accessible_status()),
        ];
    }

    /**
     * Purpose: returns human-readable names for validated fields.
     *
     * Action: helps the validator display user-friendly field names in error messages.
     *
     * @developer: M.G. Rabbi
     * @date: 2018-08-06 2:49 PM
     * @description:
     * @return array
     */
    public function attributes(): array
    {
        return [
            'is_email_verified' => __('Email Status'),
            'is_active' => __('Account Status'),
            'is_financial_active' => __('Financial Status'),
            'is_accessible_under_maintenance' => __('Maintenance Access Status'),
        ];
    }
}
