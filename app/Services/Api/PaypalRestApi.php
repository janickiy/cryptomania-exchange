<?php

namespace App\Services\Api;

class PaypalRestApi
{
    private array $config;

    public function __construct()
    {
        $this->config = config('paypal');
    }

    private function paypalAllowedCurrency()
    {
        return ['AUD', 'CAD', 'EUR', 'GBP', 'JPY', 'USD'];
    }

    /**
     * @param $amount
     * @param $currency
     * @param null $relatedTransaction
     * @return array|false
     */
    public function payment($amount, $currency, $relatedTransaction = null)
    {
        logs()->warning('PayPal REST SDK was removed during Laravel 13 upgrade; payment() requires migration to the current PayPal API.');

        return false;
    }

    /**
     * @param $paymentId
     * @param $payerId
     * @return object
     */
    public function getPaymentStatus($paymentId, $payerId)
    {
        return new class($paymentId) {
            public array $transactions;
            public object $payer;
            public string $id;

            public function __construct(string $paymentId)
            {
                $this->id = $paymentId;
                $this->transactions = [
                    (object) [
                        'amount' => (object) [
                            'total' => '0',
                            'currency' => 'USD',
                        ],
                    ],
                ];
                $this->payer = (object) [
                    'payer_info' => (object) [
                        'email' => null,
                    ],
                ];
            }

            public function getState(): string
            {
                return 'failed';
            }
        };
    }

    /**
     * @param $receiver
     * @param $value
     * @param string $currency
     * @param string $recipientType
     * @return array
     */
    public function payout($receiver, $value, $currency = 'USD', $recipientType = 'Email')
    {
        logs()->warning('PayPal REST SDK was removed during Laravel 13 upgrade; payout() requires migration to the current PayPal API.');

        return ['error' => 'PayPal payout service is not available.'];
    }
}
