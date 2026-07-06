<?php


namespace App\Repositories\User\Eloquent;

use App\Models\User\UserSetting;
use App\Repositories\BaseRepository;
use App\Repositories\User\Interfaces\UserSettingInterface;

class UserSettingRepository extends BaseRepository implements UserSettingInterface
{
    /**
     * @var UserSetting
     */
    protected $model;

    /**
     * Purpose: initializes the UserSettingRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param UserSetting $model
     */
    public function __construct(UserSetting $model)
    {
        $this->model = $model;
    }
}