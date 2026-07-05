<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Core\UserRoleManagement;
use App\Repositories\BaseRepository;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;

class UserRoleManagementRepository extends BaseRepository implements UserRoleManagementInterface
{
    /**
     * @var UserRoleManagement
     */
    protected $model;

    /**
     * @param UserRoleManagement $model
     */
    public function __construct(UserRoleManagement $model)
    {
        $this->model = $model;
    }

    public function getUserRoles(): mixed
    {
        return $this->model->where('is_active', ACTIVE_STATUS_ACTIVE)->pluck('role_name', 'id');
    }

    public function getDefaultRole(): mixed
    {
        return $this->model->where('id', admin_settings('default_role_to_register'))->firstOrFail();
    }

    /**
     * @param array $parameters
     * @return false
     * @throws \Exception
     */
    public function create(array $parameters): mixed
    {
        if ($userRole = $this->model->create($parameters)) {
            cache()->forever("userRoleManagement{$userRole->id}", $userRole->route_group);
            return $userRole;
        }

        return false;
    }

    /**
     * @param array $parameters
     * @param int $id
     * @param string $attribute
     * @return bool
     * @throws \Exception
     */
    public function update(array $parameters, int $id, string $attribute = 'id'): mixed
    {

        $userRole = $this->getFirstByConditions([$attribute => $id]);

        if ($userRole->update($parameters)) {
            cache()->forget("userRoleManagement{$userRole->id}");
            cache()->forever("userRoleManagement{$userRole->id}", $userRole->route_group);
            return true;
        }

        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById(int $id): mixed
    {
        $userRoleManagement = $this->getFirstById($id);

        if ($this->isNonDeletableRole($id)) {
            return false;
        }
        $userCount = $userRoleManagement->users->count();

        if ($userCount <= 0) {
            return $userRoleManagement->delete();
        }

        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isNonDeletableRole(int $id): mixed
    {
        $rolesFromAdminSetting =admin_settings(['default_role_to_register','signupable_user_roles']);
        $defaultRoles = config('commonconfig.fixed_roles');
        if ($rolesFromAdminSetting['default_role_to_register']==$id || in_array($id, $defaultRoles) || in_array($id, $rolesFromAdminSetting['signupable_user_roles'])) {
            return true;
        }

        return false;
    }

    /**
     * @param int $id
     * @param string $attribute
     * @return false|string
     */
    public function toggleStatusById(int $id, string $attribute ='is_active'): mixed
    {
        $userRoleManagement = $this->getFirstById($id);

        if ($this->isNonDeletableRole($id)) {
            return false;
        }

        $status = $userRoleManagement->is_active == ACTIVE_STATUS_ACTIVE ? ACTIVE_STATUS_INACTIVE : ACTIVE_STATUS_ACTIVE;
        $userRoleManagement->is_active = $status;

        if ($userRoleManagement->update()) {
            return $status == ACTIVE_STATUS_ACTIVE ? 'activated' : 'deactivated';
        }

        return false;
    }
}