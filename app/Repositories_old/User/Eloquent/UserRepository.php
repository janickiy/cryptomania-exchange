<?php


namespace App\Repositories\User\Eloquent;

use App\Models\User\User;
use App\Repositories\BaseRepository;
use App\Repositories\User\Interfaces\UserInterface;
use Illuminate\Support\Collection;

class UserRepository extends BaseRepository implements UserInterface
{
    /**
     * @var User
     */
    protected $model;

    /**
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $conditions
     * @return mixed
     */
    public function getCountByConditions(array $conditions): int
    {
        return $this->model->where($conditions)->count();
    }

    /**
     * @param array $ids
     * @param array $conditions
     * @return mixed
     */
    public function getByUserIds(array $ids, array $conditions = []): Collection
    {
        $model = $this->model->whereIn('id', $ids);

        if (!empty($conditions)) {
            $model = $model->where($conditions);
        }

        return $model->get();
    }
}
