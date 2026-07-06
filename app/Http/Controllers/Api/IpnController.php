<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\BitcoinApi;
use App\Services\Api\CoinPaymentApi;
use App\Services\User\Trader\WalletService;
use Illuminate\Http\Request;

class IpnController extends Controller
{
    /**
     * Purpose: initializes the IpnController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly WalletService $walletService)
    {
    }

    /**
     * Purpose: handles the ipn action in IpnController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     * @param Request $request
     * @return void
     *
     */
    public function ipn(Request $request): void
    {
        $ipnRequest = $request->all();

        if (env('APP_ENV') != 'production' && env('APP_DEBUG') == true) {
            logs()->info($ipnRequest);
        }

        if (empty($ipnRequest) || !isset($ipnRequest['currency'])) {
            logs()->error('log: Invalid coinpayment IPN request.');

            return;
        }

        $coinpayment = new CoinPaymentApi($ipnRequest['currency']);
        $ipnResponse = $coinpayment->validateIPN($ipnRequest, $request->server());

        if ($ipnResponse['error'] == 'ok') {
            $this->walletService->updateTransaction($ipnResponse);

            return;
        } else {
            logs()->error($ipnResponse['error']);

            return;
        }
    }

    /**
     * Purpose: handles the bitcoin ipn action in IpnController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin
     *
     * @param Request $request
     * @param string $currency
     * @return void
     */
    public function bitcoinIpn(Request $request, string $currency): void
    {
        try {
            $bitcoin = new BitcoinApi($currency);
            $ipnResponse = $bitcoin->validateIPN($request->all(), $request->server());

            if ($ipnResponse['error'] == 'ok') {
                $this->walletService->updateTransaction($ipnResponse);
            } else {
                logs()->error($ipnResponse['error']);

                return;
            }
        } catch (\Exception $exception) {
            logs()->error($exception->getMessage());

            return;
        }
    }
}
