<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 9/30/18
 * Time: 12:34 PM
 */

namespace App\Repositories\Core\Interfaces;

use App\Models\Core\UserRoleManagement;
use Illuminate\Support\Collection;

interface UserRoleManagementInterface
{
    /**
     * Purpose: describes the get user roles contract for UserRoleManagementInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getUserRoles(): Collection;

    /**
     * Purpose: describes the get default role contract for UserRoleManagementInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getDefaultRole(): UserRoleManagement;

    /**
     * Purpose: describes the create contract for UserRoleManagementInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function create(array $parameters): UserRoleManagement|false;

    /**
     * Purpose: describes the update contract for UserRoleManagementInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function update(array $parameters, int $id, string $attribute = 'id'): bool;

    /**
     * Purpose: describes the delete by id contract for UserRoleManagementInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function deleteById(int $id): bool;

    /**
     * Purpose: describes the is non deletable role contract for UserRoleManagementInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function isNonDeletableRole(int $id): bool;

    /**
     * Purpose: describes the toggle status by id contract for UserRoleManagementInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function toggleStatusById(int $id, string $attribute = 'is_active'): string|false;
}
