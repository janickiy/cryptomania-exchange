<?php

namespace App\Repositories\User\TradeAnalyst\Eloquent;

use App\Models\Backend\Post;
use App\Repositories\BaseRepository;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;
use Illuminate\Support\Collection;

class PostRepository extends BaseRepository implements PostInterface
{
    /**
     * @var Post
     */
    protected $model;

    /**
     * Purpose: initializes the PostRepository instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    /**
     * Purpose: performs the get latest by condition operation in the repository layer.
     *
     * Action: isolates database access from controllers and services.
     *
     * @param array $conditions
     * @param null $limit
     * @param array $relations
     * @return mixed
     */
    public function getLatestByCondition(array $conditions, ?int $limit = null, string|array|null $relations = []): Collection
    {
        if (is_null($limit)) {
            return $this->model->where($conditions)->with($relations)->latest()->get();
        }

        return $this->model->where($conditions)->with($relations)->take($limit)->latest()->get();
    }

}
