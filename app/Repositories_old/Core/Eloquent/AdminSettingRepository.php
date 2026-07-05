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
     * @param AdminSetting $model
     */
    public function __construct(AdminSetting $model)
    {
        $this->model = $model;
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function getBySlug(mixed $slug): AdminSetting
    {
        return $this->model->where('slug')->firstOrFail();
    }

    /**
     * @param $slugs
     * @return mixed
     */
    public function getBySlugs(mixed $slugs): array
    {
        return $this->model->whereIn('slug',$slugs)->pluck('value', 'slug')->toArray();
    }
}
