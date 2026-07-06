<?php

namespace App\Http\Controllers\TradingView;

use App\Models\User\Question;
use App\Repositories\User\Trader\Interfaces\QuestionInterface;
use App\Services\Core\DataListService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    /**
     * Purpose: initializes the FaqController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly QuestionInterface $questions,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: shows the main page or record list for the section.
     *
     * Action: collects data through services or repositories and returns the view.
     *
     */
    public function index(): View
    {
        $searchFields = [
            ['questions.title', __('Title')],
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
        ];

        $orderFields = null;
        $groupBy = ['questions.id', 'questions.title', 'questions.content', 'questions.created_at', 'users.avatar', 'first_name', 'last_name'];

        $select = ['questions.id', 'questions.title', 'questions.content', 'questions.created_at', 'users.avatar', 'first_name', 'last_name', DB::raw('count(comments.id) as comments')];
        $joinArray = [
            ['users', 'users.id', '=', 'questions.user_id'],
            ['user_infos', 'user_infos.user_id', '=', 'users.id'],
            ['comments', 'comments.commentable_id', '=', 'questions.id', ['commentable_type' => get_class(new Question())]
            ],
        ];

        $query = $this->questions->paginateWithFilters($searchFields, $orderFields, null, $select, $joinArray, $groupBy, 20);
        $data['questions'] = $this->dataListService->dataList($query, $searchFields, $orderFields, true);
        $data['title'] = __('Frequently Asked Questions');
        return view('frontend.faq.index', $data);
    }

    /**
     * Purpose: shows the detail page for the selected record.
     *
     * Action: loads the record by identifier and passes it to the view.
     *
     */
    public function show(int|string $id): View
    {
        $data['question'] = $this->questions->findOrFailByConditions(['id' => $id]);
        $data['title'] = __('Question Details');
        return view('frontend.faq.show', $data);
    }
}
