<?php

namespace App\Services\TradingView;

use App\Models\User\Question;
use App\Repositories\User\Trader\Interfaces\QuestionInterface;
use App\Services\Core\DataListService;
use Illuminate\Support\Facades\DB;

class FaqService
{
    /**
     * Purpose: initializes dependencies for public FAQ pages.
     *
     * Action: receives the question repository and list helper so the controller stays HTTP-only.
     */
    public function __construct(
        private readonly QuestionInterface $questions,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: prepares data for the public FAQ list page.
     *
     * Action: loads questions with author information and answer totals plus filter UI data.
     *
     * @return array<string, mixed>
     */
    public function indexData(): array
    {
        $searchFields = $this->searchFields();
        $orderFields = null;
        $query = $this->questions->paginateWithFilters(
            $searchFields,
            $orderFields,
            null,
            $this->indexSelect(),
            $this->indexJoins(),
            $this->indexGroupBy(),
            20
        );

        return [
            'questions' => $this->dataListService->dataList($query, $searchFields, $orderFields, true),
            'title' => __('Frequently Asked Questions'),
        ];
    }

    /**
     * Purpose: prepares data for the public FAQ detail page.
     *
     * Action: loads the selected question with author and answer relations required by the template.
     *
     * @return array<string, mixed>
     */
    public function showData(int|string $id): array
    {
        return [
            'question' => $this->questions->findOrFailByConditions(
                ['id' => $id],
                ['user.userInfo', 'comments.user.userInfo']
            ),
            'title' => __('Question Details'),
        ];
    }

    /**
     * Purpose: defines searchable fields for the FAQ list.
     *
     * Action: keeps filter configuration close to the page scenario.
     *
     * @return array<int, array<int, string>>
     */
    private function searchFields(): array
    {
        return [
            ['questions.title', __('Title')],
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
        ];
    }

    /**
     * Purpose: defines selected columns for the FAQ list query.
     *
     * Action: returns the exact fields used by the frontend list template.
     *
     * @return array<int, mixed>
     */
    private function indexSelect(): array
    {
        return [
            'questions.id',
            'questions.title',
            'questions.content',
            'questions.created_at',
            'users.avatar',
            'first_name',
            'last_name',
            DB::raw('count(comments.id) as comments'),
        ];
    }

    /**
     * Purpose: defines joins for the FAQ list query.
     *
     * Action: connects questions with authors and polymorphic comments for list rendering.
     *
     * @return array<int, array<int, mixed>>
     */
    private function indexJoins(): array
    {
        return [
            ['users', 'users.id', '=', 'questions.user_id'],
            ['user_infos', 'user_infos.user_id', '=', 'users.id'],
            ['comments', 'comments.commentable_id', '=', 'questions.id', ['commentable_type' => Question::class]],
        ];
    }

    /**
     * Purpose: defines grouped fields for the FAQ list query.
     *
     * Action: keeps aggregate comment counts compatible with SQL grouping rules.
     *
     * @return array<int, string>
     */
    private function indexGroupBy(): array
    {
        return [
            'questions.id',
            'questions.title',
            'questions.content',
            'questions.created_at',
            'users.avatar',
            'first_name',
            'last_name',
        ];
    }
}
