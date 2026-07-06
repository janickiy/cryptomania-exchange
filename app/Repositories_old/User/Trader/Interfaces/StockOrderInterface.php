<?php

namespace App\Repositories\User\Trader\Interfaces;

interface StockOrderInterface
{
    /**
     * Purpose: describes the get stop limit orders contract for StockOrderInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getStopLimitOrders(array $conditions, int|float|string $stockPrice);

    /**
     * Purpose: describes the get stop limit orders by ids contract for StockOrderInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getStopLimitOrdersByIds(array $ids);
}
