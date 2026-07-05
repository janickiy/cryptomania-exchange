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
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }
}