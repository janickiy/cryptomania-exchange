<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 10/1/18
 * Time: 12:11 PM
 */

namespace App\Repositories\User\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface UserInfoInterface
{
    /**
     * Purpose: describes the user profile creation contract.
     *
     * Action: persists prepared profile attributes and returns the created model or false.
     */
    public function create(array $attributes): Model|false;

    /**
     * Purpose: describes the user profile update contract.
     *
     * Action: updates a profile row by identifier or custom attribute and returns the updated model or false.
     */
    public function update(array $attributes, int $id, string $attribute = 'id'): Model|bool;

    /**
     * Purpose: describes the user profile lookup contract.
     *
     * Action: returns the first matching profile or aborts when it cannot be found.
     */
    public function findOrFailByConditions(array $conditions, string|array|null $relations = null): Model;

    /**
     * Purpose: describes the user profile update-by-conditions contract.
     *
     * Action: updates the first matching profile row and returns the updated model or false.
     */
    public function updateByConditions(array $attributes, array $conditions): Model|bool;

    /**
     * Purpose: describes the paginated user profile list query contract.
     *
     * Action: returns filtered user profile rows for admin tables and review lists.
     */
    public function paginateWithFilters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, int|string|null $itemPerPage = null, string $paginationKey = 'p', string $dateField = 'created_at'): LengthAwarePaginator;
}
