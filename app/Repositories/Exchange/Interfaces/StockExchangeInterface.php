<?php

namespace App\Repositories\Exchange\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface StockExchangeInterface
{
    /**
     * Purpose: describes the get latest contract for StockExchangeInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     */
    public function getLatest(array $conditions, int $limit): Collection;

    /**
     * Purpose: describes the paginated exchange report query contract.
     *
     * Action: returns filtered executed trade rows for report tables.
     */
    public function paginateWithFilters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, int|string|null $itemPerPage = null, string $paginationKey = 'p', string $dateField = 'created_at'): LengthAwarePaginator;
}
