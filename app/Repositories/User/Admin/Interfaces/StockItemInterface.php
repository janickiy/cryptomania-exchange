<?php

namespace App\Repositories\User\Admin\Interfaces;

use App\DTO\DataTransferObject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface StockItemInterface
{
    /**
     * Purpose: describes the get active list contract for StockItemInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getActiveList(int|string|null $stockItemType = null): Collection;

    /**
     * Purpose: describes the get count by conditions contract for StockItemInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getCountByConditions(array $conditions): int;

    /**
     * Purpose: describes the get stock pairs by id contract for StockItemInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getStockPairsById(int|string $id): Builder;

    /**
     * Purpose: describes the paginated stock item list query contract.
     *
     * Action: returns filtered stock item rows for admin tables.
     */
    public function paginateWithFilters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, int|string|null $itemPerPage = null, string $paginationKey = 'p', string $dateField = 'created_at'): LengthAwarePaginator;

    /**
     * Purpose: describes the stock item lookup contract by id.
     *
     * Action: returns a stock item model or fails when it cannot be found.
     */
    public function findOrFailById(int|string $id, string|array|null $relations = null): Model;

    /**
     * Purpose: describes the stock item create-from-DTO contract.
     *
     * Action: persists a stock item using normalized DTO data.
     */
    public function createFromDto(DataTransferObject $dto): Model|false;

    /**
     * Purpose: describes the stock item update-from-DTO contract.
     *
     * Action: updates a stock item using normalized DTO data.
     */
    public function updateFromDto(int|string $id, DataTransferObject $dto): bool;

    /**
     * Purpose: describes the first stock item lookup contract.
     *
     * Action: returns one matching stock item model or null.
     */
    public function getFirstById(int|string $id, string|array|null $relations = null): ?Model;

    /**
     * Purpose: describes the stock item delete contract.
     *
     * Action: deletes a stock item by primary key and reports whether it succeeded.
     */
    public function deleteById(int $id): bool;

    /**
     * Purpose: describes the stock item status toggle contract.
     *
     * Action: changes a boolean status column and returns the updated model or failure marker.
     */
    public function toggleStatusById(int $id, string $attribute = 'is_active'): Model|string|false;
}
