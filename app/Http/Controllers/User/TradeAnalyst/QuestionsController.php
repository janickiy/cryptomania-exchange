<?php

namespace App\Http\Controllers\User\TradeAnalyst;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Admin\CommentRequest;
use App\Repositories\User\Interfaces\CommentInterface;
use App\Repositories\User\Trader\Interfaces\QuestionInterface;
use App\Services\Core\DataListService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionsController extends Controller
{
    /**
     * Purpose: initializes the QuestionsController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly QuestionInterface $questionRepository,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: shows the main page or record list for the section.
     *
     * Action: collects data through services or repositories and returns the view.
     *
     */
    public function index(): View|Factory|Application
    {
        $searchFields = [
            ['questions.title', __('Title')],
        ];

        $orderFields = [
            ['questions.id', __('ID')],
            ['questions.title', __('Title')],
            ['questions.created_at', __('Created Date')],
        ];

        $select = ['questions.*', DB::raw('CONCAT(user_infos.first_name, " " , user_infos.last_name) as full_name')];
        $join = ['user_infos', 'user_infos.user_id', '=', 'questions.user_id'];

        $query = $this->questionRepository->paginateWithFilters($searchFields, $orderFields, null, $select, $join);
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Questions');

        return view('backend.questions.index', $data);
    }

    /**
     * Purpose: handles the answer form action in QuestionsController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function answerForm(int|string $id): View|Factory|Application
    {
        $data['question'] = $this->questionRepository->findOrFailById($id, ['user.userInfo', 'comments.user.userInfo']);
        $data['title'] = __('Edit Question');
        return view('backend.questions.answer', $data);
    }

    /**
     * Purpose: handles the answer action in QuestionsController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function answer(CommentRequest $request, int|string $id, CommentInterface $comment): RedirectResponse
    {
        $question = $this->questionRepository->getFirstById($id);

        if (empty($question)) {
            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Question could not found.'));
        }

        $attributes = $request->only('content');
        $attributes['user_id'] = Auth::id();


        if ($comment->save($attributes, $question)) {
            return redirect()->route('trade-analyst.questions.answer', $question->id)->with(SERVICE_RESPONSE_SUCCESS, __('Answer has been submitted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to answer.'));
    }

    /**
     * Purpose: deletes the selected record.
     *
     * Action: performs deletion through a service or repository and redirects back with the result.
     *
     */
    public function destroy(int|string $id): RedirectResponse
    {
        if ($this->questionRepository->deleteById($id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The question has been deleted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete the question.'));
    }
}
