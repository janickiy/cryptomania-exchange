<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Core\AdminSetting;
use App\Repositories\BaseRepository;
use App\Repositories\Core\Interfaces\AdminSettingInterface;


class AdminSettingRepository extends BaseRepository implements AdminSettingInterface
{
    /**
     * @var AdminSetting
     */
    protected $model;

    /**
     * Purpose: initializes the AdminSettingRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param AdminSetting $model
     */
    public function __construct(AdminSetting $model)
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
    public function getBySlug(string $slug): AdminSetting
    {
        return $this->model->where('slug')->firstOrFail();
    }

    /**
     * Purpose: performs the get by slugs operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param $slugs
     * @return mixed
     */
    public function getBySlugs(array $slugs): array
    {
        return $this->model->whereIn('slug',$slugs)->pluck('value', 'slug')->toArray();
    }
}
