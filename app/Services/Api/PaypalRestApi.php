<?php

namespace App\Services\Api;

class PaypalRestApi
{
    private array $config;

    /**
     * Purpose: initializes the PaypalRestApi instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct()
    {
        $this->config = config('paypal');
    }

    /**
     * Purpose: executes the paypal allowed currency service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    private function paypalAllowedCurrency(): array
    {
        return ['AUD', 'CAD', 'EUR', 'GBP', 'JPY', 'USD'];
    }

    /**
     * Purpose: executes the payment service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @param $amount
     * @param $currency
     * @param null $relatedTransaction
     * @return array|false
     */
    public function payment(int|float|string $amount, string $currency, ?array $relatedTransaction = null): array|false
    {
        logs()->warning('PayPal REST SDK was removed during Laravel 13 upgrade; payment() requires migration to the current PayPal API.');

        return false;
    }

    /**
     * Purpose: executes the get payment status service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @param $paymentId
     * @param $payerId
     * @return object
     */
    public function getPaymentStatus(string $paymentId, string $payerId): object
    {
        return new class($paymentId) {
            public array $transactions;
            public object $payer;
            public string $id;

            /**
             * Purpose: initializes the anonymous class instance.
             *
             * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
             *
             */
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

            /**
             * Purpose: executes the get state service operation.
             *
             * Action: contains scenario business logic and keeps controllers free from processing details.
             *
             */
            public function getState(): string
            {
                return 'failed';
            }
        };
    }

    /**
     * Purpose: executes the payout service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @param $receiver
     * @param $value
     * @param string $currency
     * @param string $recipientType
     * @return array
     */
    public function payout(string $receiver, int|float|string $value, string $currency = 'USD', string $recipientType = 'Email'): array
    {
        logs()->warning('PayPal REST SDK was removed during Laravel 13 upgrade; payout() requires migration to the current PayPal API.');

        return ['error' => 'PayPal payout service is not available.'];
    }
}
