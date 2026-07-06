<?php

namespace App\Repositories\User\Admin\Interfaces;

use App\Models\Backend\StockPair;
use Illuminate\Support\Collection;

interface StockPairInterface
{
    /**
     * Purpose: describes the get by pair contract for StockPairInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     */
    public function getByPair(string $stockItem, string $baseItem): ?StockPair;

    /**
     * Purpose: describes the active stock pair detail list contract for StockPairInterface.
     *
     * Action: returns stock pair detail records matching the provided conditions.
     *
     * @param array<string, int|string> $conditions
     */
    public function getAllStockPairDetailByConditions(array $conditions): Collection;
}
