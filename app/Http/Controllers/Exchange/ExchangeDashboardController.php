<?php

namespace App\Http\Controllers\Exchange;

use App\Http\Controllers\Controller;
use App\Services\Exchange\ExchangeDashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExchangeDashboardController extends Controller
{
    /**
     * Purpose: initializes the ExchangeDashboardController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     */
    public function __construct(private readonly ExchangeDashboardService $exchangeService)
    {
    }

    /**
     * Purpose: shows the exchange dashboard page.
     *
     * Action: renders the exchange interface with view data prepared by the service layer.
     */
    public function index(?string $pair = null): View
    {
        return view('frontend.exchange.index', $this->exchangeService->viewData($pair));
    }

    /**
     * Purpose: returns 24-hour pair statistics.
     *
     * Action: delegates pair summary calculation to the exchange service and returns JSON.
     */
    public function get24HrPairDetail(int|string $stockPairID): JsonResponse
    {
        return response()->json($this->exchangeService->get24HrPairDetail($stockPairID));
    }

    /**
     * Purpose: returns active exchange market pairs.
     *
     * Action: delegates stock market formatting to the exchange service and returns JSON.
     */
    public function getStockMarket(): JsonResponse
    {
        return response()->json($this->exchangeService->getStockMarketResponse());
    }

    /**
     * Purpose: returns order book rows for the selected pair.
     *
     * Action: passes request filters to the exchange service and returns JSON.
     */
    public function getOrders(Request $request): JsonResponse
    {
        return response()->json($this->exchangeService->getOrders(
            $request->input('stock_pair_id'),
            $request->input('last_price'),
            $request->input('exchange_type', EXCHANGE_SELL),
            $request->input('exchange_category', CATEGORY_EXCHANGE),
        ));
    }

    /**
     * Purpose: returns chart data for the selected pair.
     *
     * Action: delegates chart aggregation to the exchange service and stores chart cookies on the response.
     */
    public function getChartData(Request $request): JsonResponse
    {
        return response()
            ->json($this->exchangeService->getChartData(
                $request->input('stock_pair_id'),
                $request->input('interval')
            ))
            ->cookie('stockPairID', $request->input('stock_pair_id'))
            ->cookie('chartInterval', $request->input('interval'));
    }

    /**
     * Purpose: returns authenticated user's open orders for the selected pair.
     *
     * Action: delegates user-specific order lookup to the exchange service and returns JSON.
     */
    public function getMyOpenOrders(Request $request): JsonResponse
    {
        return response()->json($this->exchangeService->getMyOpenOrders($request->input('stock_pair_id')));
    }

    /**
     * Purpose: returns recent public trade history for the selected pair.
     *
     * Action: delegates trade history lookup to the exchange service and returns JSON.
     */
    public function getTradeHistories(Request $request): JsonResponse
    {
        return response()->json($this->exchangeService->getTradeHistories($request->input('stock_pair_id')));
    }

    /**
     * Purpose: returns authenticated user's recent trades for the selected pair.
     *
     * Action: delegates user-specific trade history lookup to the exchange service and returns JSON.
     */
    public function getMyTrade(Request $request): JsonResponse
    {
        return response()->json($this->exchangeService->getMyTrade($request->input('stock_pair_id')));
    }

    /**
     * Purpose: returns authenticated user's wallet summary for the selected pair.
     *
     * Action: delegates wallet balance lookup to the exchange service and returns JSON.
     */
    public function getWalletSummary(Request $request): JsonResponse
    {
        return response()->json($this->exchangeService->getWalletSummary($request->input('stock_pair_id')));
    }
}
