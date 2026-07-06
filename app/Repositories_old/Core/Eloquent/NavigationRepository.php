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
     * Purpose: initializes the NavigationRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param Navigation $model
     */
    public function __construct(Navigation $model)
    {
        $this->model = $model;
    }

    /**
     * Purpose: performs the get by slug operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $slug
     * @return mixed
     */
    public function getBySlug(string $slug): ?Navigation
    {
        return $this->model->where('slug', $slug)->first();
    }
}
