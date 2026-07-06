<?php

namespace App\Http\Requests\User\Admin;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StockPairRequest extends Request
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
        $stockPairRequest = [
            'stock_item_id' => 'required|exists:stock_items,id,is_active,' . ACTIVE_STATUS_ACTIVE,
            'base_item_id' => 'required|different:stock_item_id|exists:stock_items,id,is_active,' . ACTIVE_STATUS_ACTIVE,
            'last_price' => 'required|numeric|between:0.00000001, 99999999999.99999999',
        ];

        if ($this->isMethod('POST')) {
            $stockPairRequest['is_active'] = 'required|in:' . array_to_string(active_status());
            $stockPairRequest['is_default'] = 'required|in:' . array_to_string(active_status());
        }

        return $stockPairRequest;
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
            'stock_item_id' => __('Active status'),
            'base_item_id' => __('Base Item'),
            'last_price' => __('Initial Price'),
            'is_active' => __('Active Status'),
        ];
    }
}
