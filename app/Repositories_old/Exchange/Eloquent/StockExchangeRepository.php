<?php

namespace App\Repositories\Exchange\Eloquent;

use App\Models\Backend\StockExchange;
use App\Repositories\BaseRepository;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use Illuminate\Support\Collection;

class StockExchangeRepository extends BaseRepository implements StockExchangeInterface
{
    /**
     * @var StockExchange
     */
    protected $model;

    /**
     * Purpose: initializes the StockExchangeRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param StockExchange $stockExchange
     */
    public function __construct(StockExchange $stockExchange)
    {
        $this->model = $stockExchange;
    }

    /**
     * Purpose: performs the get latest operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param array $conditions
     * @param int $limit
     * @return mixed
     */
    public function getLatest(array $conditions, int $limit): Collection
    {
        return $this->model
            ->select(['stock_exchanges.price','stock_exchanges.amount','stock_exchanges.exchange_type', 'stock_exchanges.created_at as date'])
            ->leftJoin('stock_orders', 'stock_orders.id', '=','stock_exchanges.stock_order_id')
            ->where($conditions)
            ->orderBy('stock_exchanges.id', 'desc')
            ->take($limit)
            ->get();
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
