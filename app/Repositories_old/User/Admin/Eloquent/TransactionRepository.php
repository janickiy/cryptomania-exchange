<?php

namespace App\Repositories\User\Admin\Eloquent;

use App\Models\Backend\Transaction;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\BaseRepository;

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
}