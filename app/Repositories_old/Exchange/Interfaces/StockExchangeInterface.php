<?php

namespace App\Repositories\Exchange\Interfaces;

interface StockExchangeInterface
{
    /**
     * Purpose: describes the get latest contract for StockExchangeInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getLatest(array $conditions, int $limit);
}