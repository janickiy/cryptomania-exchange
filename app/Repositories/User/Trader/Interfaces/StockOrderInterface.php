<?php

namespace App\Repositories\User\Trader\Interfaces;

use App\Models\User\StockOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface StockOrderInterface
{
    /**
     * Purpose: describes the grouped order book query contract for StockOrderInterface.
     *
     * Action: returns aggregated pending orders for the provided filter conditions.
     */
    public function getOrders(array $conditions): Collection;

    /**
     * Purpose: describes the current user's open orders query contract for StockOrderInterface.
     *
     * Action: returns open orders matching the provided filter conditions.
     */
    public function getMyOpenOrders(array $conditions): Collection;

    /**
     * Purpose: describes the total order summary query contract for StockOrderInterface.
     *
     * Action: returns aggregated totals for pending orders matching the provided conditions.
     */
    public function getTotalStockOrder(array $conditions): ?StockOrder;

    /**
     * Purpose: describes the get stop limit orders contract for StockOrderInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     */
    public function getStopLimitOrders(array $conditions, int|float|string $stockPrice): Collection;

    /**
     * Purpose: describes the get stop limit orders by ids contract for StockOrderInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     */
    public function getStopLimitOrdersByIds(array $ids): Collection;

    /**
     * Purpose: describes the paginated open-order report query contract.
     *
     * Action: returns filtered pending order rows for report tables.
     */
    public function paginateWithFilters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, int|string|null $itemPerPage = null, string $paginationKey = 'p', string $dateField = 'created_at'): LengthAwarePaginator;
}
