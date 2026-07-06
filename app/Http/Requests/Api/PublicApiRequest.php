<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class PublicApiRequest extends Request
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
        $rules = [
            'command' => "required|in:" . array_to_string(allowed_public_api_command(), ',', false),
            'coinPair' => "required_if:command,returnChartData",
            'interval' => "required_if:command,returnChartData|in:" . array_to_string(chart_data_interval()),
        ];

        return $rules;
    }
}
