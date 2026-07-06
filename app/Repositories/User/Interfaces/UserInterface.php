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
use Illuminate\Support\Collection;

interface UserInterface
{
    /**
     * Purpose: describes the get count by conditions contract for UserInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getCountByConditions(array $conditions): int;

    /**
     * Purpose: describes the get by user ids contract for UserInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getByUserIds(array $ids, array $conditions = []): Collection;

    /**
     * Purpose: describes the user lookup by identifier contract.
     *
     * Action: returns the requested user model or fails when the model does not exist.
     */
    public function findOrFailById(int|string $id, string|array|null $relations = null): Model;

    /**
     * Purpose: describes the first user lookup by identifier contract.
     *
     * Action: returns a user model when it exists or null when it is missing.
     */
    public function getFirstById(int|string $id, string|array|null $relations = null): ?Model;

    /**
     * Purpose: describes the first user lookup by arbitrary conditions contract.
     *
     * Action: returns a user model matching the conditions or null when no row matches.
     */
    public function getFirstByConditions(array $conditions, string|array|null $relations = null): ?Model;

    /**
     * Purpose: describes the user creation contract.
     *
     * Action: persists prepared user attributes and returns the created model or false.
     */
    public function create(array $attributes): Model|false;

    /**
     * Purpose: describes the user update contract.
     *
     * Action: updates one user row by identifier and returns the updated model or false.
     */
    public function update(array $attributes, int $id, string $attribute = 'id'): Model|bool;

    /**
     * Purpose: describes the paginated user report query contract.
     *
     * Action: returns filtered user rows for report tables.
     */
    public function paginateWithFilters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, int|string|null $itemPerPage = null, string $paginationKey = 'p', string $dateField = 'created_at'): LengthAwarePaginator;
}
