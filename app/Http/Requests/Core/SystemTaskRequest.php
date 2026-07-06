<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Request;

class SystemTaskRequest extends Request
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
            "module_id" => 'required|numeric',
            "name" => 'required|unique:system_tasks,name,'.$this->route('system_task'),
            "icon" => 'required',
            "route" => 'required',
            "order" => 'numeric',
        ];
    }
}
