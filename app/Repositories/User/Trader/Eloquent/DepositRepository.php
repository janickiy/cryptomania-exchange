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

    /**
     * Purpose: initializes the DepositRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(Deposit $deposit)
    {
        $this->model = $deposit;
    }

    /**
     * Purpose: performs the update or create operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param array $attributes
     * @param array $conditions
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $conditions): Deposit
    {
        return $this->model->updateOrCreate($conditions, $attributes);
    }

    /**
     * Purpose: performs the first or create operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param array $attributes
     * @return mixed
     */
    public function firstOrCreate(array $attributes): Deposit
    {
        return $this->model->firstOrCreate($attributes);
    }

    /**
     * Purpose: performs the first or fail operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param array $conditions
     * @param null $relations
     * @return mixed
     */
    public function firstOrFail(array $conditions, string|array|null $relations = null): Deposit
    {
        return $this->model->where($conditions)->with($this->extractToArray($relations))->firstOrFail();
    }
}
