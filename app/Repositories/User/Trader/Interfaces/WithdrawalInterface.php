<?php

namespace App\Repositories\User\Trader\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface WithdrawalInterface
{
    /**
     * Purpose: describes the paginated withdrawal report query contract.
     *
     * Action: returns filtered withdrawal rows for report tables.
     */
    public function paginateWithFilters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, int|string|null $itemPerPage = null, string $paginationKey = 'p', string $dateField = 'created_at'): LengthAwarePaginator;
}
