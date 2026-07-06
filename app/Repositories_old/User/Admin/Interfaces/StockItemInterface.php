<?php

namespace App\Repositories\User\Admin\Interfaces;

interface StockItemInterface
{
    /**
     * Purpose: describes the get active list contract for StockItemInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getActiveList(int|string|null $stockItemType = null);

    /**
     * Purpose: describes the get count by conditions contract for StockItemInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getCountByConditions(array $conditions);

    /**
     * Purpose: describes the get stock pairs by id contract for StockItemInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getStockPairsById(int|string $id);
}
