<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UserRoleManagementRequest extends Request
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
        if ($this->route()->getName() == 'user-role-managements.update') {
            return [
                'role_name' => [
                    'required',
                    Rule::unique('user_role_managements', 'role_name')->ignore($this->route()->parameter('id')),
                ],
                'roles' => 'array',
            ];
        } else {
            return [
                'role_name' => 'required|unique:user_role_managements,role_name',
                'roles' => 'array',
            ];
        }
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
            'role_name.required' => __('The role name field is required.'),
            'role_name.unique' => __('The role name has already been taken.'),
        ];
    }

    /**
     * Purpose: prepares the validator instance for the current request.
     *
     * Action: adds extra validation checks or transformations before the standard Laravel validation flow.
     *
     */
    public function getValidatorInstance(): Validator
    {
        $validator = parent::getValidatorInstance();
        $validator->after(function () use ($validator) {
            $routeConfigs = config('permissionRoutes.configurable_routes');
            $roles = $this->input('roles', []);

            foreach ($roles as $roleKey => $roleValue) {
                foreach ($roleValue as $roleGroupKey => $roleGroupValue) {
                    foreach ($roleGroupValue as $key => $role) {
                        if (!isset($routeConfigs[$roleKey][$roleGroupKey][$role])) {
                            unset($roles[$roleKey][$roleGroupKey][$key]);
                        }
                    }
                    if (empty($roles[$roleKey][$roleGroupKey])) {
                        unset($roles[$roleKey][$roleGroupKey]);
                    }
                }
                if (empty($roles[$roleKey])) {
                    unset($roles[$roleKey]);
                }
            }
            $this->merge(['roles' => $roles]);
            if (empty($roles)) {
                $validator->errors()->add('roles', __('The roles must have at least one access selected.'));
            }
        });
        return $validator;
    }
}
