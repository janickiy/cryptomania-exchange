<?php

namespace App\Services\Guest;

use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;
use Illuminate\Support\Collection;

class HomePageService
{
    private const LATEST_POST_LIMIT = 3;

    /**
     * Purpose: initializes the HomePageService instance.
     *
     * Action: receives repositories required to build the public home page data.
     */
    public function __construct(
        private readonly StockPairInterface $stockPairs,
        private readonly PostInterface $posts,
    ) {
    }

    /**
     * Purpose: prepares data required by the public home page.
     *
     * Action: collects active stock pairs and latest published posts for the view layer.
     *
     * @return array{title: string, stockPairs: Collection, posts: Collection}
     */
    public function viewData(): array
    {
        return [
            'title' => __('Home'),
            'stockPairs' => $this->activeStockPairs(),
            'posts' => $this->latestPublishedPosts(),
        ];
    }

    /**
     * Purpose: returns active trading pairs for the home ticker.
     *
     * Action: delegates the stock pair query to the repository with active pair conditions.
     */
    private function activeStockPairs(): Collection
    {
        return $this->stockPairs->getAllStockPairDetailByConditions($this->activeStockPairConditions());
    }

    /**
     * Purpose: returns latest published trade analyst posts for the home page.
     *
     * Action: loads the posts with comments through the repository.
     */
    private function latestPublishedPosts(): Collection
    {
        return $this->posts->getLatestByCondition(
            $this->publishedPostConditions(),
            self::LATEST_POST_LIMIT,
            ['comments']
        );
    }

    /**
     * Purpose: defines active stock pair filter conditions.
     *
     * Action: keeps repository filter values in one place.
     *
     * @return array<string, int>
     */
    private function activeStockPairConditions(): array
    {
        return ['stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE];
    }

    /**
     * Purpose: defines published post filter conditions.
     *
     * Action: keeps repository filter values in one place.
     *
     * @return array<string, int>
     */
    private function publishedPostConditions(): array
    {
        return ['is_published' => ACTIVE_STATUS_ACTIVE];
    }
}
