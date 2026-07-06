<?php

namespace App\Services\Api;

interface RealCurrencyApiInterface
{
    /**
     * Purpose: describes the allowed currency contract for RealCurrencyApiInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function allowedCurrency();

    /**
     * Purpose: describes the payment contract for RealCurrencyApiInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function payment(int|float|string $amount, string $currency, ?array $relatedTransaction = null);

    /**
     * Purpose: describes the get payment status contract for RealCurrencyApiInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getPaymentStatus(string $paymentId, string $payerId);

    /**
     * Purpose: describes the payout contract for RealCurrencyApiInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function payout(string $receiver, int|float|string $value, string $currency = 'USD', string $recipientType = 'Email');
}
