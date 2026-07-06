<?php

namespace App\Repositories\User\TradeAnalyst\Eloquent;

use App\Models\Backend\Post;
use App\Repositories\BaseRepository;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
     * @param array<string, mixed> $conditions
     * @param array<int, string>|string|null $relations
     */
    public function getLatestByCondition(array $conditions, ?int $limit = null, string|array|null $relations = []): Collection
    {
        if (is_null($limit)) {
            return $this->model->where($conditions)->with($relations)->latest()->get();
        }

        return $this->model->where($conditions)->with($relations)->take($limit)->latest()->get();
    }

    /**
     * Purpose: builds the public trading view list query.
     *
     * Action: hides joins, grouping, publication status, and comment totals behind the repository contract.
     */
    public function publishedTradingViews(array $searchFields, array $orderFields, int $perPage): LengthAwarePaginator
    {
        $where = ['is_published' => ACTIVE_STATUS_ACTIVE];
        $select = [
            'posts.id',
            'posts.title',
            'posts.content',
            'posts.featured_image',
            'posts.created_at',
            'users.avatar',
            'first_name',
            'last_name',
            DB::raw('count(comments.id) as comments'),
        ];
        $joinArray = [
            ['users', 'users.id', '=', 'posts.user_id'],
            ['user_infos', 'users.id', '=', 'user_infos.user_id'],
            ['comments', 'comments.commentable_id', '=', 'posts.id', ['commentable_type' => Post::class]],
        ];
        $groupBy = [
            'posts.id',
            'posts.title',
            'posts.content',
            'posts.featured_image',
            'posts.created_at',
            'users.avatar',
            'first_name',
            'last_name',
        ];

        return $this->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray, $groupBy, $perPage);
    }

    /**
     * Purpose: finds one published post for the public detail page.
     *
     * Action: eager loads relations used by the template to avoid hidden lazy-loading work.
     */
    public function findPublishedTradingView(int|string $id): Post
    {
        return $this->model
            ->with(['user.userInfo', 'comments.user.userInfo'])
            ->where(['id' => $id, 'is_published' => ACTIVE_STATUS_ACTIVE])
            ->firstOrFail();
    }

    /**
     * Purpose: finds a post that can receive a comment.
     *
     * Action: preserves the existing comment behavior by looking up the post by id.
     */
    public function findCommentable(int|string $id): ?Post
    {
        return $this->model->where('id', $id)->first();
    }

}
