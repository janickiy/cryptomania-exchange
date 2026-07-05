<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Core\Navigation;
use App\Repositories\BaseRepository;
use App\Repositories\Core\Interfaces\NavigationInterface;

class NavigationRepository extends BaseRepository implements NavigationInterface
{
    /**
     * @var Navigation
     */
    protected $model;

    /**
     * @param Navigation $model
     */
    public function __construct(Navigation $model)
    {
        $this->model = $model;
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function getBySlug(mixed $slug): mixed
    {
        return $this->model->where('slug', $slug)->first();
    }
}