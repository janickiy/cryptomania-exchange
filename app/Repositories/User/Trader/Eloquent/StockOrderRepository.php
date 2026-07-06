<?php

namespace App\Repositories\User\Trader\Eloquent;

use App\Models\User\StockOrder;
use App\Repositories\BaseRepository;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockOrderRepository extends BaseRepository implements StockOrderInterface
{
    /**
     * @var StockOrder
     */
    protected $model;

    /**
     * Purpose: initializes the StockOrderRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param StockOrder $stockOrder
     */
    public function __construct(StockOrder $stockOrder)
    {
        $this->model = $stockOrder;
    }

    /**
     * Purpose: performs the get opposite stock orders operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param StockOrder $stockOrder
     * @return \Illuminate\Support\Collection
     */
    public function getOppositeStockOrders(StockOrder $stockOrder): Collection
    {
        $oppositeStockOrderType = $stockOrder->exchange_type == EXCHANGE_BUY ? EXCHANGE_SELL : EXCHANGE_BUY;
        $sort = $stockOrder->exchange_type == EXCHANGE_BUY ? 'asc' : 'desc';
        $operator = $stockOrder->exchange_type == EXCHANGE_BUY ? '<=' : '>=';
        $tableName = $this->model->getTable();

        return DB::table(DB::raw("(select * from {$tableName} where price {$operator} {$stockOrder->price} and stock_pair_id = {$stockOrder->stock_pair_id} and exchange_type = {$oppositeStockOrderType} and status = 1 order by price {$sort}) as stocks, (select @tempsum :=0) as tempsum"))
            ->select('*', DB::raw('@tempsum := @tempsum + (amount - exchanged) as cumulative_sum'))
            ->whereRaw("@tempsum < $stockOrder->amount")
            ->get();
    }

    /**
     * Purpose: performs the get orders operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $conditions
     * @return mixed
     */
    public function getOrders(array $conditions): Collection
    {
        $orderBy = $conditions['exchange_type'] == EXCHANGE_BUY ? 'desc' : 'asc';
        return $this->model
            ->where($conditions)
            ->select('price', DB::raw('truncate(sum(amount - exchanged), 8) as amount'), DB::raw('truncate((price*sum(amount - exchanged)), 8) as total'))
            ->orderBy('price', $orderBy)
            ->groupBy('price')
            ->take(ORDER_PER_PAGE)
            ->get();
    }

    /**
     * Purpose: performs the get my open orders operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $conditions
     * @return mixed
     */
    public function getMyOpenOrders(array $conditions): Collection
    {
        return $this->model
            ->where($conditions)
            ->select('id as order_number', 'price', DB::raw('TRUNCATE((amount - exchanged),8) as amount'), 'exchange_type', 'stop_limit', 'created_at as date')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Purpose: performs the get total stock order operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $conditions
     * @return mixed
     */
    public function getTotalStockOrder(array $conditions): ?StockOrder
    {
        return $this->model
            ->where($conditions)
            ->select(DB::raw('truncate(sum((amount - exchanged)*price), 8) as base_total'), DB::raw('truncate(sum(amount - exchanged), 8) as item_total'))
            ->first();
    }

    /**
     * Purpose: performs the get stop limit orders operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $conditions
     * @param $stockPrice
     * @return mixed
     */
    public function getStopLimitOrders(array $conditions, int|float|string $stockPrice): Collection
    {
        return $this->model->where($conditions)->where(function ($q) use ($stockPrice) {
            $q->where(['exchange_type' => EXCHANGE_BUY, ['stop_limit', '<=', $stockPrice]])
                ->orWhere(['exchange_type' => EXCHANGE_SELL, ['stop_limit', '>=', $stockPrice]]);
        })->get();
    }

    /**
     * Purpose: performs the get stop limit orders by ids operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $ids
     * @return mixed
     */
    public function getStopLimitOrdersByIds(array $ids): Collection
    {
        return $this->model->whereIn('id', $ids)->where('status', STOCK_ORDER_PENDING)->get();
    }

    /**
     * Purpose: performs the count operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param array $conditions
     * @return mixed
     */
    public function count(array $conditions): int
    {
        return $this->model->where($conditions)->count();
    }
}
