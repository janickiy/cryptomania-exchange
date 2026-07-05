<?php

namespace App\Http\Controllers\Exchange;

use App\Http\Controllers\Controller;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Services\Exchange\ExchangeDashboardService;
use App\Services\Exchange\StockGraphDataService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ExchangeDashboardController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела биржевой панели.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly ExchangeDashboardService $exchangeService,
        private readonly StockGraphDataService $stockGraphDataService,
        private readonly StockOrderInterface $stockOrders,
        private readonly StockExchangeInterface $stockExchanges,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела биржевой панели.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(?string $pair = null): View|Factory|Application
    {
        $data['title'] = __('Exchange');
        $data['stockPair'] = $this->exchangeService->getDefaultStockPair($pair);

        abort_if(empty($data['stockPair']), 404, __('Exchange not found for this pair.'));

        $data['categoryID'] = CATEGORY_EXCHANGE;

        $data['chartInterval'] = Cookie::get('chartInterval');
        if (empty($data['chartInterval'])) {
            $data['chartInterval'] = 240;
            cookie()->forever('chartInterval', $data['chartInterval']);
        }

        $data['chartZoom'] = Cookie::get('chartZoom');
        if (empty($data['chartZoom'])) {
            $data['chartZoom'] = 20160;
            cookie()->forever('chartZoom', $data['chartZoom']);
        }

        return view('frontend.exchange.index', $data);
    }

    /**
     * Назначение: возвращает 24-часовую сводку по торговой паре.
     *
     * Действие: запрашивает статистику пары через сервис биржи и отдает JSON-ответ.
     */
    public function get24HrPairDetail(int|string $stockPairID): JsonResponse
    {
        $response = $this->exchangeService->get24HrPairDetail($stockPairID);

        return response()->json($response);
    }

    /**
     * Назначение: возвращает список доступных рынков.
     *
     * Действие: получает торговые рынки, группирует базовые активы и отдает JSON-ответ.
     */
    public function getStockMarket(): JsonResponse
    {
        $stockMarkets = $this->exchangeService->getStockMarket();
        $baseItems = [];
        foreach ($stockMarkets as $stockMarket) {
            $baseItems[$stockMarket->base_item_id] = $stockMarket->base_item_abbr;
        }

        $response = [
            'stockItems' => $stockMarkets->toArray(),
            'baseItems' => $baseItems,
        ];
        return response()->json($response);
    }

    /**
     * Назначение: возвращает стакан ордеров для торговой пары.
     *
     * Действие: читает параметры пары и цены из запроса, получает ордера через сервис и отдает JSON.
     */
    public function getOrders(Request $request): JsonResponse
    {
        $response = $this->exchangeService->getOrders($request->stock_pair_id, $request->last_price, $request->exchange_type, $request->exchange_category);


        return response()->json($response);
    }

    /**
     * Назначение: возвращает данные графика торговой пары.
     *
     * Действие: получает график по паре и интервалу, сохраняет настройки в cookie и отдает JSON.
     */
    public function getChartData(Request $request): JsonResponse
    {
        $chartData = $this->stockGraphDataService->getGraphData($request->stock_pair_id, $request->interval);
//        $chartData = json_decode(file_get_contents(asset('dummy-chart-data.json')), true);
        return response()->json($chartData)->cookie('stockPairID', $request->stock_pair_id)->cookie('chartInterval', $request->interval);
    }

    /**
     * Назначение: возвращает открытые ордера текущего пользователя.
     *
     * Действие: фильтрует ордера по авторизованному пользователю и торговой паре, затем отдает JSON.
     */
    public function getMyOpenOrders(Request $request): JsonResponse
    {
        $conditions = [
            'user_id' => Auth::id(),
            'stock_pair_id' => $request->stock_pair_id,
            ['status', '<', STOCK_ORDER_COMPLETED]
        ];
        $myOpenOrders = $this->stockOrders->getMyOpenOrders($conditions);
        return response()->json($myOpenOrders);
    }

    /**
     * Назначение: возвращает историю сделок торговой пары.
     *
     * Действие: формирует условия выборки по паре и категории, получает сделки и отдает JSON.
     */
    public function getTradeHistories(Request $request): JsonResponse
    {
        $conditions = [
            'stock_exchanges.stock_pair_id' => $request->stock_pair_id,
            'stock_orders.category' => CATEGORY_EXCHANGE,
            'stock_exchanges.is_maker' => 1
        ];

        $tradeHistories = $this->stockExchanges->getLatest($conditions, TRADE_HISTORY_PER_PAGE);
        return response()->json($tradeHistories);
    }

    /**
     * Назначение: возвращает сделки текущего пользователя.
     *
     * Действие: фильтрует сделки по авторизованному пользователю и торговой паре, затем отдает JSON.
     */
    public function getMyTrade(Request $request): JsonResponse
    {
        $conditions = [
            'stock_exchanges.stock_pair_id' => $request->stock_pair_id,
            'stock_orders.category' => CATEGORY_EXCHANGE,
            'stock_exchanges.user_id' => Auth::id()
        ];

        $tradeHistories = $this->stockExchanges->getLatest($conditions, TRADE_HISTORY_PER_PAGE);
        return response()->json($tradeHistories);
    }

    /**
     * Назначение: возвращает краткую сводку кошелька для торговой пары.
     *
     * Действие: получает балансы пользователя по выбранной паре и отдает JSON.
     */
    public function getWalletSummary(Request $request): JsonResponse
    {
        $walletSummary = $this->exchangeService->getWalletSummary($request->stock_pair_id);
        return response()->json($walletSummary);
    }


}
