<?php

namespace App\Repositories\User\Trader\Eloquent;

use App\Repositories\User\Trader\Interfaces\DepositInterface;
use App\Models\User\Deposit;
use App\Repositories\BaseRepository;

class DepositRepository extends BaseRepository implements DepositInterface
{
    /**
     * @var Deposit
     */
    protected $model;

    public function __construct(Deposit $deposit)
    {
        $this->model = $deposit;
    }

    /**
     * @param array $attributes
     * @param array $conditions
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $conditions)
    {
        return $this->model->updateOrCreate($conditions, $attributes);
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function firstOrCreate(array $attributes)
    {
        return $this->model->firstOrCreate($attributes);
    }

    /**
     * @param array $conditions
     * @param null $relations
     * @return mixed
     */
    public function firstOrFail(array $conditions, $relations = null)
    {
        return $this->model->where($conditions)->with($this->extractToArray($relations))->firstOrFail();
    }
}