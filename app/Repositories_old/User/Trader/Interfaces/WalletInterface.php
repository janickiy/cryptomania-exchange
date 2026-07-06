<?php

namespace App\Repositories\User\Trader\Interfaces;

interface WalletInterface
{
    /**
     * Purpose: describes the find stock item contract for WalletInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function findStockItem(int $id);

    /**
     * Purpose: describes the insert contract for WalletInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function insert(array $parameters);
}