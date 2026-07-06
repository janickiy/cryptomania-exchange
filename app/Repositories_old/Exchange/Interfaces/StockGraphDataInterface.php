<?php

namespace App\Repositories\Exchange\Interfaces;

interface StockGraphDataInterface
{
    /**
     * Purpose: describes the update or create contract for StockGraphDataInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function updateOrCreate(array $conditions, array $attributes);
}
