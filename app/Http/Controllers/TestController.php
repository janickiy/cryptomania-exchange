<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\IpnController;
use App\Jobs\StopLimitStockOrder;
use App\Models\User\StockOrder;
use App\Repositories\User\Interfaces\UserInterface;
use App\Services\Api\BitcoinApi;

class TestController extends Controller
{
    /**
     * Назначение: выполняет тестовый endpoint.
     *
     * Действие: запускает временную проверочную логику без возврата пользовательского представления.
     */
    public function test(): void
    {

    }
}
