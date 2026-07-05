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
     * Назначение: инициализирует контроллер раздела платежных IPN-уведомлений.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(private readonly WalletService $walletService)
    {
    }

    /**
     * Назначение: обрабатывает IPN-уведомление CoinPayments.
     *
     * Действие: передает тело callback-запроса в сервис кошельков и завершает обработку без вывода страницы.
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
     * Назначение: обрабатывает Bitcoin IPN-уведомление.
     *
     * Действие: передает валюту и данные callback-запроса в сервис кошельков для проверки транзакции.
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
