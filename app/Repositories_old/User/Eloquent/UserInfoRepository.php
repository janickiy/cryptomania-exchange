<?php


namespace App\Repositories\User\Eloquent;

use App\Models\User\UserInfo;
use App\Repositories\BaseRepository;
use App\Repositories\User\Interfaces\UserInfoInterface;

class UserInfoRepository extends BaseRepository implements UserInfoInterface
{
    /**
     * @var UserInfo
     */
    protected $model;

    /**
     * Purpose: initializes the UserInfoRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param UserInfo $model
     */
    public function __construct(UserInfo $model)
    {
        $this->model = $model;
    }
}