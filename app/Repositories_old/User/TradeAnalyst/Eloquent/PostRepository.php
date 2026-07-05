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
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    /**
     * @param array $conditions
     * @param null $limit
     * @param array $relations
     * @return mixed
     */
    public function getLatestByCondition(array $conditions, mixed $limit = null, mixed $relations = []): Collection
    {
        if (is_null($limit)) {
            return $this->model->where($conditions)->with($relations)->latest()->get();
        }

        return $this->model->where($conditions)->with($relations)->take($limit)->latest()->get();
    }

}
