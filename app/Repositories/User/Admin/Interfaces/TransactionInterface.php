<?php

namespace App\Repositories\User\Admin\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TransactionInterface
{
    /**
     * Purpose: describes the transaction bulk insert contract.
     *
     * Action: persists prepared transaction rows from wallet, order, and exchange services.
     */
    public function insert(array $attributes): bool;

    /**
     * Purpose: describes the paginated transaction report query contract.
     *
     * Action: returns filtered transaction rows for report tables.
     */
    public function paginateWithFilters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, int|string|null $itemPerPage = null, string $paginationKey = 'p', string $dateField = 'created_at'): LengthAwarePaginator;

    /**
     * Purpose: describes the transaction summary query contract.
     *
     * Action: returns filtered grouped transaction totals for report summary tables.
     */
    public function summary(array $searchFields, array $orderFields, ?array $whereArray, array $joinArray): Collection;
}
