<?php

namespace App\Repositories\User\Trader\Interfaces;

use App\Models\User\Wallet;

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
     * Purpose: describes the wallet lookup contract for report pages.
     *
     * Action: returns a wallet with optional relations or fails when it cannot be found.
     */
    public function firstOrFail(array $conditions, string|array|null $relations = null): Wallet;
}
