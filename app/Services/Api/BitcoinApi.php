<?php

namespace App\Services\Api;

use Denpa\Bitcoin\Client as BitcoinClient;
use Illuminate\Support\Arr;

class BitcoinApi extends Bitcoind
{

    protected $bitcoind;
    protected $currency;
    protected $networkFee;

    /***
     * @param $currency
     */
    /**
     * Purpose: initializes the BitcoinApi instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(string $currency)
    {
        $this->currency = $currency;
        $configuration = config(strtolower($currency));

        if (empty($configuration)) {
            return ['error' => __('Configuration not found for this :currency.', ['currency' => $currency])];
        }

        $this->networkFee = bcmul($configuration['network_fee'], "1");
        $configuration = Arr::except($configuration, 'network_fee');

        if (empty($configuration['user']) || empty($configuration['password'])) {
            return ['error' => __('Deposit / Withdrawal is currently disabled for this stock item.')];
        }

        $this->bitcoind = new BitcoinClient($configuration);
    }
}
