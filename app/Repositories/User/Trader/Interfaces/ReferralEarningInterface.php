<?php

namespace App\Repositories\User\Trader\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface ReferralEarningInterface
{
    /**
     * Purpose: describes the referral earning report query contract.
     *
     * Action: returns grouped referral earning rows without pagination.
     */
    public function filters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, string $paginationKey = 'p', string $dateField = 'created_at'): Collection;
}
