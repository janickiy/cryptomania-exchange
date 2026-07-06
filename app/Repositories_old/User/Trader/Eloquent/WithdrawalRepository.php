<?php

namespace App\Repositories\User\Trader\Eloquent;

use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use App\Models\User\Withdrawal;
use App\Repositories\BaseRepository;

class WithdrawalRepository extends BaseRepository implements WithdrawalInterface
{
    /**
    * @var Withdrawal
    */
     protected $model;

     /**
      * Purpose: initializes the WithdrawalRepository instance.
      *
      * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
      *
      */
     public function __construct(Withdrawal $withdrawal)
     {
        $this->model = $withdrawal;
     }

    /**
     * Purpose: performs the get last24hr withrawal amount operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param array $conditions
     * @return mixed
     */
     public function getLast24hrWithrawalAmount(array $conditions): int|float|string
     {
         return $this->model->where($conditions)->where('created_at', '>=', now()->subDay())->sum('amount');
     }
}
