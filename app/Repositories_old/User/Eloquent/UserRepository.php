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
     * Purpose: initializes the UserRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Purpose: performs the get count by conditions operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param array $conditions
     * @return mixed
     */
    public function getCountByConditions(array $conditions): int
    {
        return $this->model->where($conditions)->count();
    }

    /**
     * Purpose: performs the get by user ids operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
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
