<?php

namespace App\Repositories\User\TradeAnalyst\Interfaces;

use App\Models\Backend\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PostInterface
{
    /**
     * Purpose: describes the get latest by condition contract for PostInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     * @param array<string, int|string> $conditions
     * @param array<int, string>|string|null $relations
     */
    public function getLatestByCondition(array $conditions, ?int $limit = null, string|array|null $relations = []): Collection;

    /**
     * Purpose: describes the public trading view list query contract.
     *
     * Action: returns published posts with author data and comment totals for the public list page.
     */
    public function publishedTradingViews(array $searchFields, array $orderFields, int $perPage): LengthAwarePaginator;

    /**
     * Purpose: describes the public trading view detail lookup contract.
     *
     * Action: returns a published post or fails when the post is not visible.
     */
    public function findPublishedTradingView(int|string $id): Post;

    /**
     * Purpose: describes the comment target lookup contract.
     *
     * Action: returns the post that should receive a comment or null when it cannot be found.
     */
    public function findCommentable(int|string $id): ?Post;
}
