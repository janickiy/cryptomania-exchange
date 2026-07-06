<?php

namespace App\Services\TradingView;

use App\DTO\TradingView\CommentData;
use App\Repositories\User\Interfaces\CommentInterface;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;
use App\Services\Core\DataListService;
use Illuminate\Support\Facades\Auth;

class TradingViewService
{
    /**
     * Purpose: initializes dependencies for public trading view pages.
     *
     * Action: receives repositories and list helpers so controllers remain HTTP-only.
     */
    public function __construct(
        private readonly PostInterface $posts,
        private readonly CommentInterface $comments,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: prepares data for the public trading views list.
     *
     * Action: loads published posts with author and comment totals plus filter UI data.
     */
    public function indexData(): array
    {
        $searchFields = $this->searchFields();
        $orderFields = $this->orderFields();
        $query = $this->posts->publishedTradingViews($searchFields, $orderFields, 6);

        return [
            'posts' => $this->dataListService->dataList($query, $searchFields, $orderFields),
            'title' => __('Trading Views'),
        ];
    }

    /**
     * Purpose: prepares data for a public trading view detail page.
     *
     * Action: loads one published post with the relations used by the detail template.
     */
    public function showData(int|string $id): array
    {
        return [
            'post' => $this->posts->findPublishedTradingView($id),
            'title' => __('Trading View'),
        ];
    }

    /**
     * Purpose: creates a comment for a trading view post.
     *
     * Action: validates the target post, builds a DTO, saves the comment, and returns an operation result.
     */
    public function comment(int|string $id, string $content): array
    {
        $post = $this->posts->findCommentable($id);

        if (empty($post)) {
            return $this->response(false, __('Post could not found.'));
        }

        $commentData = CommentData::fromArray([
            'content' => $content,
            'user_id' => $this->currentUserId(),
        ]);

        if ($this->comments->save($commentData->toArray(), $post)) {
            return $this->response(true, __('Comment has been submitted successfully.'), $post->id);
        }

        return $this->response(false, __('Failed to place comment.'));
    }

    /**
     * Purpose: defines searchable fields for the public trading views list.
     *
     * Action: keeps filter configuration close to the page scenario.
     */
    private function searchFields(): array
    {
        return [
            ['posts.title', __('Title')],
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
        ];
    }

    /**
     * Purpose: defines sortable fields for the public trading views list.
     *
     * Action: keeps ordering configuration close to the page scenario.
     */
    private function orderFields(): array
    {
        return [
            ['posts.title', __('Title')],
            ['posts.created_at', __('Date')],
        ];
    }

    /**
     * Purpose: returns the authenticated user identifier for comment creation.
     *
     * Action: centralizes Auth access and aborts if the request is no longer authenticated.
     */
    private function currentUserId(): int|string
    {
        $userId = Auth::id();

        abort_if(is_null($userId), 401, __('Unauthorized access!'));

        return $userId;
    }

    /**
     * Purpose: builds a service operation response.
     *
     * Action: standardizes status, message, and optional post id for controller redirects.
     */
    private function response(bool $status, string $message, int|string|null $postId = null): array
    {
        return [
            SERVICE_RESPONSE_STATUS => $status,
            SERVICE_RESPONSE_MESSAGE => $message,
            'post_id' => $postId,
        ];
    }
}
