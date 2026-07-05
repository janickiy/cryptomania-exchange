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
     * @param StockGraphData $stockGraphData
     */
    public function __construct(StockGraphData $stockGraphData)
    {
        $this->model = $stockGraphData;
    }

    /**
     * @param $conditions
     * @param $attributes
     * @return mixed
     */
    public function updateOrCreate($conditions, $attributes)
    {
        return $this->model->updateOrCreate($conditions, $attributes);
    }
}