<?php

namespace App\Repositories\User\Admin\Interfaces;

interface StockPairInterface
{
    /**
     * Purpose: describes the get by pair contract for StockPairInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    function getByPair(string $stockItem, string $baseItem);
}
