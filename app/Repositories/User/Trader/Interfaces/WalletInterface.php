<?php

namespace App\Repositories\User\Trader\Interfaces;

use App\Models\User\Wallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface WalletInterface
{
    /**
     * Purpose: describes the find stock item contract for WalletInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function findStockItem(int $id): ?Wallet;

    /**
     * Purpose: describes the insert contract for WalletInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function insert(array $parameters): bool;

    /**
     * Purpose: describes the wallet lookup by conditions contract.
     *
     * Action: returns the first matching wallet with optional relations or null when it is missing.
     */
    public function getFirstByConditions(array $conditions, string|array|null $relations = null): ?Model;

    /**
     * Purpose: describes the wallet update contract.
     *
     * Action: updates one wallet row by identifier and returns the updated model or false.
     */
    public function update(array $attributes, int $id, string $attribute = 'id'): Model|bool;

    /**
     * Purpose: describes the wallet lookup contract for report pages.
     *
     * Action: returns a wallet with optional relations or fails when it cannot be found.
     */
    public function firstOrFail(array $conditions, string|array|null $relations = null): Wallet;

    /**
     * Purpose: describes the paginated wallet query contract.
     *
     * Action: returns filtered wallet rows for admin and trader wallet tables.
     */
    public function paginateWithFilters(array $searchFields, ?array $orderFields = null, ?array $whereArray = null, array|string|null $selectData = null, ?array $joinArray = null, array|string|null $groupBy = null, int|string|null $itemPerPage = null, string $paginationKey = 'p', string $dateField = 'created_at'): LengthAwarePaginator;
}
