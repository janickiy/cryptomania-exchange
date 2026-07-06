<?php

namespace App\Repositories\User\TradeAnalyst\Interfaces;

interface PostInterface
{
    /**
     * Purpose: describes the get latest by condition contract for PostInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getLatestByCondition(array $conditions, ?int $limit = null);
}
