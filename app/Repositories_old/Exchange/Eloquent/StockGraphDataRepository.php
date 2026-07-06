<?php

namespace App\Repositories\Exchange\Eloquent;

use App\Models\Backend\StockGraphData;
use App\Repositories\Exchange\Interfaces\StockGraphDataInterface;
use App\Repositories\BaseRepository;

class StockGraphDataRepository extends BaseRepository implements StockGraphDataInterface
{
    /**
     * @var StockGraphData
     */
    protected $model;

    /**
     * Purpose: initializes the StockGraphDataRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param StockGraphData $stockGraphData
     */
    public function __construct(StockGraphData $stockGraphData)
    {
        $this->model = $stockGraphData;
    }

    /**
     * Purpose: performs the update or create operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $conditions
     * @param $attributes
     * @return mixed
     */
    public function updateOrCreate(array $conditions, array $attributes): StockGraphData
    {
        return $this->model->updateOrCreate($conditions, $attributes);
    }
}
