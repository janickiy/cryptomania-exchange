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
     * Назначение: инициализирует контроллер раздела вопросов и ответов.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly QuestionInterface $questionRepository,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела вопросов и ответов.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
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
     * Назначение: показывает форму ответа на вопрос трейдера.
     *
     * Действие: загружает вопрос по идентификатору и возвращает представление формы ответа.
     */
    public function answerForm(int|string $id): View|Factory|Application
    {
        $data['question'] = $this->questionRepository->findOrFailById($id, ['user.userInfo', 'comments.user.userInfo']);
        $data['title'] = __('Edit Question');
        return view('backend.questions.answer', $data);
    }

    /**
     * Назначение: сохраняет ответ аналитика на вопрос.
     *
     * Действие: создает комментарий-ответ, связывает его с вопросом и возвращает результат операции.
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
     * Назначение: удаляет запись в разделе вопросов и ответов.
     *
     * Действие: проверяет возможность удаления, выполняет операцию через сервис или репозиторий и возвращает результат.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        if ($this->questionRepository->deleteById($id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The question has been deleted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete the question.'));
    }
}
