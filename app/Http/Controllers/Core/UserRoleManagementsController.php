<?php

namespace App\Http\Controllers\Core;

use App\DTO\Admin\UserRoleManagementData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRoleManagementRequest;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;
use App\Services\Core\DataListService;
use App\Services\User\Admin\UserRoleManagementService;


class UserRoleManagementsController extends Controller
{
    public $roleManagement;

    public function __construct(UserRoleManagementInterface $roleManagement)
    {
        $this->roleManagement = $roleManagement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $searchFields = [
            ['role_name', __('Role Name')],
        ];
        $orderFields = [
            ['id', __('Serial')],
            ['role_name', __('Role Name')],
        ];

        $query = $this->roleManagement->paginateWithFilters($searchFields, $orderFields);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Role Management');
        $data['defaultRoles'] = config('commonconfig.fixed_roles');
        if (!is_array($data['defaultRoles'])) {
            $data['defaultRoles'] = [];
        }

        return view('backend.userRoleManagements.index', $data);
    }

    public function create()
    {
        $data['routes'] = config('permissionRoutes.configurable_routes');
        $data['title'] = __('Create User Role');

        return view('backend.userRoleManagements.create', $data);
    }

    /**
     * @param UserRoleManagementRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(UserRoleManagementRequest $request)
    {
        if ($userRoleManagement = app(UserRoleManagementService::class)->create(UserRoleManagementData::fromArray($request->validated()))) {
            cache()->forever("userRoleManagement" . $userRoleManagement->id, $userRoleManagement->route_group);
            return redirect()->route('user-role-managements.edit', $userRoleManagement->id)->with(SERVICE_RESPONSE_SUCCESS, __('User role has been created successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to create user role.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['routes'] = config('permissionRoutes.configurable_routes');
        $data['userRoleManagement'] = $this->roleManagement->findOrFailById($id);
        $data['title'] = __('Edit User Role');

        return view('backend.userRoleManagements.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRoleManagementRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRoleManagementRequest $request, $id)
    {
        if (app(UserRoleManagementService::class)->update((int) $id, UserRoleManagementData::fromArray($request->validated()))) {
            return redirect()->route('user-role-managements.edit', $id)->with(SERVICE_RESPONSE_SUCCESS, __('User role has been updated successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to update user role.'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($this->roleManagement->deleteById($id)) {
            return redirect()->route('user-role-managements.index')->with(SERVICE_RESPONSE_SUCCESS, __('User role has been deleted successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('This role cannot be deleted.'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus($id)
    {
        if ($updatedState = $this->roleManagement->toggleStatusById($id)) {
            return redirect()->route('user-role-managements.index')->with(SERVICE_RESPONSE_SUCCESS, __('User role has been :state successfully', ['state' => $updatedState]));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('User role status can not be changed'));
    }
}
