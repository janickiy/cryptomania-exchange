<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Core\UserRoleManagement;
use App\Repositories\BaseRepository;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;
use Illuminate\Support\Collection;

class UserRoleManagementRepository extends BaseRepository implements UserRoleManagementInterface
{
    /**
     * @var UserRoleManagement
     */
    protected $model;

    /**
     * Purpose: initializes the UserRoleManagementRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param UserRoleManagement $model
     */
    public function __construct(UserRoleManagement $model)
    {
        $this->model = $model;
    }

    /**
     * Purpose: performs the get user roles operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     */
    public function getUserRoles(): Collection
    {
        return $this->model->where('is_active', ACTIVE_STATUS_ACTIVE)->pluck('role_name', 'id');
    }

    /**
     * Purpose: performs the get default role operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     */
    public function getDefaultRole(): UserRoleManagement
    {
        return $this->model->where('id', admin_settings('default_role_to_register'))->firstOrFail();
    }

    /**
     * Purpose: creates a new record in storage.
     *
     * Action: accepts prepared data or a DTO and persists only fields allowed by the model.
     *
     * @param array $parameters
     * @return false
     * @throws \Exception
     */
    public function create(array $parameters): UserRoleManagement|false
    {
        if ($userRole = $this->model->create($parameters)) {
            cache()->forever("userRoleManagement{$userRole->id}", $userRole->route_group);
            return $userRole;
        }

        return false;
    }

    /**
     * Purpose: updates one or more records in storage.
     *
     * Action: centralizes data changes and returns the result to the service layer.
     *
     * @param array $parameters
     * @param int $id
     * @param string $attribute
     * @return bool
     * @throws \Exception
     */
    public function update(array $parameters, int $id, string $attribute = 'id'): bool
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
     * Purpose: removes records from storage.
     *
     * Action: encapsulates delete operations and their result in the repository layer.
     *
     * @param int $id
     * @return bool
     */
    public function deleteById(int $id): bool
    {
        $userRoleManagement = $this->getFirstById($id);

        if ($this->isNonDeletableRole($id)) {
            return false;
        }
        $userCount = $userRoleManagement->users->count();

        if ($userCount <= 0) {
            return (bool) $userRoleManagement->delete();
        }

        return false;
    }

    /**
     * Purpose: performs the is non deletable role operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param int $id
     * @return bool
     */
    public function isNonDeletableRole(int $id): bool
    {
        $rolesFromAdminSetting =admin_settings(['default_role_to_register','signupable_user_roles']);
        $defaultRoles = config('commonconfig.fixed_roles');
        if ($rolesFromAdminSetting['default_role_to_register']==$id || in_array($id, $defaultRoles) || in_array($id, $rolesFromAdminSetting['signupable_user_roles'])) {
            return true;
        }

        return false;
    }

    /**
     * Purpose: performs the toggle status by id operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param int $id
     * @param string $attribute
     * @return false|string
     */
    public function toggleStatusById(int $id, string $attribute ='is_active'): string|false
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
