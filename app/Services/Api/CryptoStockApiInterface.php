<?php

namespace App\Services\Api;

interface CryptoStockApiInterface
{
    /**
     * Purpose: describes the generate address contract for CryptoStockApiInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function generateAddress();

    /**
     * Purpose: describes the get txn info by address contract for CryptoStockApiInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getTxnInfoByAddress(string $address);

    /**
     * Purpose: describes the get txn info by txn id contract for CryptoStockApiInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getTxnInfoByTxnId(string $txid);

    /**
     * Purpose: describes the get txn list contract for CryptoStockApiInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getTxnList(int $limit = 25);

    /**
     * Purpose: describes the send to address contract for CryptoStockApiInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function sendToAddress(string $address, int|float|string $amount);

    /**
     * Purpose: describes the validate ipn contract for CryptoStockApiInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function validateIPN(array $post_data, array $server_data);
}
