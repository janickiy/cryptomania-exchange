<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Admin settings are configured dynamically in config/adminsetting.php;
     * AdminSettingService keeps field-specific validation.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
