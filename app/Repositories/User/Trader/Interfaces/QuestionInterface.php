<?php

namespace App\Repositories\User\Trader\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface QuestionInterface
{
    /**
     * Purpose: describes the question creation contract.
     *
     * Action: persists prepared question attributes and returns the created model or false.
     */
    public function create(array $attributes): Model|false;

    /**
     * Purpose: describes the question lookup contract by id.
     *
     * Action: returns a question model or fails when it cannot be found.
     */
    public function findOrFailById(int|string $id, string|array|null $relations = null): Model;

    /**
     * Purpose: describes the first question lookup contract by id.
     *
     * Action: returns a question model when it exists or null when it is missing.
     */
    public function getFirstById(int|string $id, string|array|null $relations = null): ?Model;

    /**
     * Purpose: describes the question lookup contract by conditions.
     *
     * Action: returns the first matching question or fails when it cannot be found.
     */
    public function findOrFailByConditions(array $conditions, string|array|null $relations = null): Model;

    /**
     * Purpose: describes the paginated question list query contract.
     *
     * Action: returns filtered question rows for frontend and admin tables.
     */
    public function paginateWithFilters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, int|string|null $itemPerPage = null, string $paginationKey = 'p', string $dateField = 'created_at'): LengthAwarePaginator;

    /**
     * Purpose: describes the question delete contract.
     *
     * Action: deletes a question by primary key and reports whether it succeeded.
     */
    public function deleteById(int $id): bool;
}
