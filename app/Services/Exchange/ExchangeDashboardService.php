<?php

namespace App\Services\Exchange;

use App\Models\Backend\StockPair;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ExchangeDashboardService
{
    private const DEFAULT_CHART_INTERVAL = 240;
    private const DEFAULT_CHART_ZOOM = 20160;

    /**
     * Purpose: initializes the ExchangeDashboardService instance.
     *
     * Action: receives repositories and helper services used by exchange dashboard scenarios.
     */
    public function __construct(
        private readonly StockPairInterface $stockPairs,
        private readonly StockOrderInterface $stockOrders,
        private readonly StockExchangeInterface $stockExchanges,
        private readonly WalletInterface $wallets,
        private readonly StockGraphDataService $stockGraphDataService,
    ) {
    }

    /**
     * Purpose: prepares data required by the exchange dashboard page.
     *
     * Action: resolves the selected stock pair and chart preferences for the view layer.
     *
     * @return array{title: string, stockPair: StockPair, categoryID: int, chartInterval: int|string, chartZoom: int|string}
     */
    public function viewData(?string $pair = null): array
    {
        $stockPair = $this->getDefaultStockPair($pair);

        abort_if(empty($stockPair), 404, __('Exchange not found for this pair.'));

        return [
            'title' => __('Exchange'),
            'stockPair' => $stockPair,
            'categoryID' => CATEGORY_EXCHANGE,
            'chartInterval' => $this->chartInterval(),
            'chartZoom' => $this->chartZoom(),
        ];
    }

    /**
     * Purpose: executes the get default stock pair service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @return StockPair|null
     */
    public function getDefaultStockPair(?string $pair): ?StockPair
    {
        $stockPair = null;

        if (empty($pair)) {
            $stockPairId = Cookie::get('stockPairID');

            if (empty($stockPairId)) {
                $stockPair = $this->stockPairs->getFirstByConditions(['is_active' => ACTIVE_STATUS_ACTIVE, 'is_default' => ACTIVE_STATUS_ACTIVE]);
                if (!empty($stockPair)) {
                    cookie()->forever('stockPairID', $stockPair->id);
                }
            } else {
                $stockPair = $this->stockPairs->getFirstById($stockPairId);
            }
        } else {
            $pairParts = explode('-', $pair, 2);

            if (count($pairParts) !== 2) {
                return null;
            }

            $stockItem = strtoupper($pairParts[0]);
            $baseItem = strtoupper($pairParts[1]);
            $stockPair = $this->stockPairs->getByPair($stockItem, $baseItem);

            if (!empty($stockPair)) {
                cookie()->forever('stockPairID', $stockPair->id);
            }
        }

        return $stockPair;
    }

    /**
     * Purpose: executes the get24hr pair detail service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @return array|false
     */
    public function get24HrPairDetail(int|string $stockPairID): array|false
    {
        $conditions = [
            'stock_pairs.id' => $stockPairID,
            'stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE,
            'stock_item.is_active' => ACTIVE_STATUS_ACTIVE,
            'base_item.is_active' => ACTIVE_STATUS_ACTIVE,
            'base_item.is_ico' => ACTIVE_STATUS_INACTIVE,
            'stock_item.is_ico' => ACTIVE_STATUS_INACTIVE,
            'base_item.exchange_status' => ACTIVE_STATUS_ACTIVE,
            'stock_item.exchange_status' => ACTIVE_STATUS_ACTIVE,
        ];

        $get24hrPairData = $this->stockPairs->getFirstStockPairDetailByConditions($conditions);

        if (empty($get24hrPairData)) {
            return false;
        }

        $pair24HrDetail = [
            'baseItem' => $get24hrPairData->base_item_abbr,
            'stockItem' => $get24hrPairData->stock_item_abbr,
            'lastPrice' => $get24hrPairData->last_price,
            'change24hrInPercent' => $get24hrPairData->change_24,
            'high24hr' => $get24hrPairData->high_24,
            'low24hr' => $get24hrPairData->low_24,
            'baseVolume' => $get24hrPairData->exchanged_base_item_volume_24,
            'stockVolume' => $get24hrPairData->exchanged_stock_item_volume_24
        ];

        return $pair24HrDetail;
    }

    /**
     * Purpose: executes the get stock market service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function getStockMarket(): Collection
    {
        return $this->stockPairs->getAllStockPairDetailByConditions($this->activeStockMarketConditions());
    }

    /**
     * Purpose: returns stock market data formatted for the dashboard API.
     *
     * Action: groups base item labels beside the stock item list.
     *
     * @return array{stockItems: array<int, array<string, bool|float|int|string|null>>, baseItems: array<int|string, string>}
     */
    public function getStockMarketResponse(): array
    {
        $stockMarkets = $this->getStockMarket();

        return [
            'stockItems' => $stockMarkets->toArray(),
            'baseItems' => $this->baseItems($stockMarkets),
        ];
    }

    /**
     * Purpose: executes the get orders service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @return array
     */
    public function getOrders(
        int|string|null $stockPairID,
        int|float|string|null $lastPrice = null,
        int|string $exchangeType = EXCHANGE_SELL,
        int|string $category = CATEGORY_EXCHANGE
    ): array
    {
        $conditions = [
            'stock_pair_id' => $stockPairID,
            'category' => $category,
            'exchange_type' => $exchangeType,
            'status' => STOCK_ORDER_PENDING
        ];

        if (!empty($lastPrice)) {
            if ($exchangeType == EXCHANGE_SELL) {
                array_push($conditions, ['price', '>', $lastPrice]);
            } else {
                array_push($conditions, ['price', '<', $lastPrice]);
            }
        }


        $stockOrders = $this->stockOrders->getOrders($conditions);
        $totalStockOrders = $this->stockOrders->getTotalStockOrder($conditions);

        return [
            'stockOrders' => $stockOrders->toArray(),
            'totalStockOrder' => $totalStockOrders
        ];
    }

    /**
     * Purpose: returns chart data for the selected stock pair.
     *
     * Action: delegates graph aggregation to the graph data service.
     */
    public function getChartData(int|string|null $stockPairId, int|string|null $interval): array
    {
        return $this->stockGraphDataService->getGraphData($stockPairId, $interval);
    }

    /**
     * Purpose: returns current user's open orders for a stock pair.
     *
     * Action: builds the authenticated user conditions and delegates the query to the repository.
     */
    public function getMyOpenOrders(int|string|null $stockPairId): Collection
    {
        return $this->stockOrders->getMyOpenOrders([
            'user_id' => Auth::id(),
            'stock_pair_id' => $stockPairId,
            ['status', '<', STOCK_ORDER_COMPLETED],
        ]);
    }

    /**
     * Purpose: returns recent public trade history for a stock pair.
     *
     * Action: builds exchange trade filters and delegates the query to the repository.
     */
    public function getTradeHistories(int|string|null $stockPairId): Collection
    {
        return $this->stockExchanges->getLatest($this->tradeHistoryConditions($stockPairId), TRADE_HISTORY_PER_PAGE);
    }

    /**
     * Purpose: returns recent authenticated user trades for a stock pair.
     *
     * Action: builds user-specific trade filters and delegates the query to the repository.
     */
    public function getMyTrade(int|string|null $stockPairId): Collection
    {
        return $this->stockExchanges->getLatest($this->myTradeConditions($stockPairId), TRADE_HISTORY_PER_PAGE);
    }

    /**
     * Purpose: executes the get wallet summary service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @return array
     */
    public function getWalletSummary(int|string|null $stockPairId): array
    {
        $this->wallets->createUnavailableWallet(Auth::id());

        $stockPair = $this->stockPairs->getFirstById($stockPairId);

        $conditions = [
            'stock_item_id' => $stockPair->base_item_id,
            'user_id' => Auth::id()
        ];

        $baseItemWallet = $this->wallets->getFirstByConditions($conditions);

        $conditions['stock_item_id'] = $stockPair->stock_item_id;

        $stockItemWallet = $this->wallets->getFirstByConditions($conditions);

        return [
            'base_item_balance' => $baseItemWallet->primary_balance,
            'base_item_on_order' => $baseItemWallet->on_order_balance,
            'stock_item_balance' => $stockItemWallet->primary_balance,
            'stock_item_on_order' => $stockItemWallet->on_order_balance,
        ];
    }

    /**
     * Purpose: returns the selected chart interval from cookies.
     *
     * Action: falls back to the default value and preserves the existing cookie behavior.
     */
    private function chartInterval(): int|string
    {
        return $this->cookieValue('chartInterval', self::DEFAULT_CHART_INTERVAL);
    }

    /**
     * Purpose: returns the selected chart zoom from cookies.
     *
     * Action: falls back to the default value and preserves the existing cookie behavior.
     */
    private function chartZoom(): int|string
    {
        return $this->cookieValue('chartZoom', self::DEFAULT_CHART_ZOOM);
    }

    /**
     * Purpose: returns a dashboard cookie value with a default fallback.
     *
     * Action: creates the default cookie value when the incoming cookie is empty.
     */
    private function cookieValue(string $name, int $default): int|string
    {
        $value = Cookie::get($name);

        if (empty($value)) {
            cookie()->forever($name, $default);

            return $default;
        }

        return $value;
    }

    /**
     * Purpose: returns filters for active exchange stock market pairs.
     *
     * Action: keeps stock market availability conditions in one place.
     *
     * @return array<string, int>
     */
    private function activeStockMarketConditions(): array
    {
        return [
            'stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE,
            'stock_item.is_active' => ACTIVE_STATUS_ACTIVE,
            'base_item.is_active' => ACTIVE_STATUS_ACTIVE,
            'base_item.is_ico' => ACTIVE_STATUS_INACTIVE,
            'stock_item.is_ico' => ACTIVE_STATUS_INACTIVE,
            'base_item.exchange_status' => ACTIVE_STATUS_ACTIVE,
            'stock_item.exchange_status' => ACTIVE_STATUS_ACTIVE,
        ];
    }

    /**
     * Purpose: extracts base item labels from stock market records.
     *
     * Action: builds the base item map expected by exchange frontend scripts.
     *
     * @return array<int|string, string>
     */
    private function baseItems(Collection $stockMarkets): array
    {
        $baseItems = [];

        foreach ($stockMarkets as $stockMarket) {
            $baseItems[$stockMarket->base_item_id] = $stockMarket->base_item_abbr;
        }

        return $baseItems;
    }

    /**
     * Purpose: returns filters for public trade history.
     *
     * Action: keeps trade history repository conditions in one place.
     *
     * @return array<string, int|string|null>
     */
    private function tradeHistoryConditions(int|string|null $stockPairId): array
    {
        return [
            'stock_exchanges.stock_pair_id' => $stockPairId,
            'stock_orders.category' => CATEGORY_EXCHANGE,
            'stock_exchanges.is_maker' => 1,
        ];
    }

    /**
     * Purpose: returns filters for authenticated user's trade history.
     *
     * Action: extends public trade filters with the current user ID.
     *
     * @return array<string, int|string|null>
     */
    private function myTradeConditions(int|string|null $stockPairId): array
    {
        return array_merge($this->tradeHistoryConditions($stockPairId), [
            'stock_exchanges.user_id' => Auth::id(),
        ]);
    }
}
