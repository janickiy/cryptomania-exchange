<?php

namespace App\Services\User\Admin;

use App\DTO\Admin\UserRoleManagementData;
use App\Models\Core\UserRoleManagement;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;

class UserRoleManagementService
{
    public function __construct(private readonly UserRoleManagementInterface $roleManagement)
    {
    }

    public function create(UserRoleManagementData $data): UserRoleManagement|false
    {
        return $this->roleManagement->create($data->toArray());
    }

    public function update(int $id, UserRoleManagementData $data): bool
    {
        $roles = $data->roles;

        if ($id === USER_ROLE_SUPER_ADMIN) {
            $roles[ROUTE_SECTION_USER_MANAGEMENTS][ROUTE_SUB_SECTION_ROLE_MANAGEMENTS] = [
                ROUTE_GROUP_READER_ACCESS,
                ROUTE_GROUP_CREATION_ACCESS,
                ROUTE_GROUP_MODIFIER_ACCESS,
                ROUTE_GROUP_DELETION_ACCESS,
            ];
        }

        return (bool) $this->roleManagement->update([
            'role_name' => $data->roleName,
            'route_group' => $roles,
        ], $id);
    }
}
