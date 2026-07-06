<?php

namespace App\Repositories\User\Admin\Eloquent;

use App\Models\Backend\Transaction;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TransactionRepository extends BaseRepository implements TransactionInterface
{
    /**
     * @var Transaction
     */
    protected $model;

    /**
     * Purpose: initializes the TransactionRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }

    /**
     * Purpose: builds grouped transaction totals for admin reports.
     *
     * Action: runs the filtered summary query in the repository so SQL aggregation stays out of services.
     */
    public function summary(array $searchFields, array $orderFields, ?array $whereArray, array $joinArray): Collection
    {
        $select = ['stock_items.item', 'journal', DB::raw('sum(amount) as amount')];

        return $this->filters(
            $searchFields,
            $orderFields,
            $whereArray,
            $select,
            $joinArray,
            ['stock_items.item', 'journal']
        );
    }
}
