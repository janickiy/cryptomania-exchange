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
     * Purpose: handles the test action in TestController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function test(): void
    {

    }
}
