<?php

namespace App\Repositories\Exchange\Eloquent;

use App\Repositories\Exchange\Interfaces\StockExchangeGroupInterface;
use App\Models\Exchange\StockExchangeGroup;
use App\Repositories\BaseRepository;

class StockExchangeGroupRepository extends BaseRepository implements StockExchangeGroupInterface
{
    /**
    * @var StockExchangeGroup
    */
     protected $model;

    /**
     * Purpose: initializes the StockExchangeGroupRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param StockExchangeGroup $stockExchangeGroup
     */
     public function __construct(StockExchangeGroup $stockExchangeGroup)
     {
        $this->model = $stockExchangeGroup;
     }
}