<?php

namespace App\Repositories\User\Trader\Eloquent;

use App\Models\User\StockOrder;
use App\Repositories\BaseRepository;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use Illuminate\Support\Facades\DB;

class StockOrderRepository extends BaseRepository implements StockOrderInterface
{
    /**
     * @var StockOrder
     */
    protected $model;

    /**
     * @param StockOrder $stockOrder
     */
    public function __construct(StockOrder $stockOrder)
    {
        $this->model = $stockOrder;
    }

    /**
     * @param StockOrder $stockOrder
     * @return \Illuminate\Support\Collection
     */
    public function getOppositeStockOrders(StockOrder $stockOrder)
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
     * @param $conditions
     * @return mixed
     */
    public function getOrders($conditions)
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
     * @param $conditions
     * @return mixed
     */
    public function getMyOpenOrders($conditions)
    {
        return $this->model
            ->where($conditions)
            ->select('id as order_number', 'price', DB::raw('TRUNCATE((amount - exchanged),8) as amount'), 'exchange_type', 'stop_limit', 'created_at as date')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * @param $conditions
     * @return mixed
     */
    public function getTotalStockOrder($conditions)
    {
        return $this->model
            ->where($conditions)
            ->select(DB::raw('truncate(sum((amount - exchanged)*price), 8) as base_total'), DB::raw('truncate(sum(amount - exchanged), 8) as item_total'))
            ->first();
    }

    /**
     * @param $conditions
     * @param $stockPrice
     * @return mixed
     */
    public function getStopLimitOrders($conditions, $stockPrice)
    {
        return $this->model->where($conditions)->where(function ($q) use ($stockPrice) {
            $q->where(['exchange_type' => EXCHANGE_BUY, ['stop_limit', '<=', $stockPrice]])
                ->orWhere(['exchange_type' => EXCHANGE_SELL, ['stop_limit', '>=', $stockPrice]]);
        })->get();
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function getStopLimitOrdersByIds($ids)
    {
        return $this->model->whereIn('id', $ids)->where('status', STOCK_ORDER_PENDING)->get();
    }

    /**
     * @param array $conditions
     * @return mixed
     */
    public function count(array $conditions)
    {
        return $this->model->where($conditions)->count();
    }
}