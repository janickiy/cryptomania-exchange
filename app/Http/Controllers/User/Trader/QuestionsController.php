<?php

namespace App\Http\Controllers\User\Trader;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\TradeAnalyst\QuestionRequest;
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

        $conditions = ['questions.user_id' => Auth::id()];
        $select = ['questions.*', DB::raw('CONCAT(user_infos.first_name, " " , user_infos.last_name) as full_name')];
        $join = ['user_infos', 'user_infos.user_id', '=', 'questions.user_id'];

        $query = $this->questionRepository->paginateWithFilters($searchFields, $orderFields, $conditions, $select, $join);
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Questions');

        return view('backend.questions.index', $data);
    }

    /**
     * Назначение: показывает форму создания записи в разделе вопросов и ответов.
     *
     * Действие: подготавливает справочные данные для формы и возвращает представление создания.
     */
    public function create(): View|Factory|Application
    {
        $data['title'] = __('Create Question');
        return view('backend.questions.create', $data);
    }

    /**
     * Назначение: создает новую запись в разделе вопросов и ответов.
     *
     * Действие: принимает валидированный запрос, передает данные в сервис или репозиторий и возвращает результат операции.
     */
    public function store(QuestionRequest $request): RedirectResponse
    {
        $attributes = $request->only(['title', 'content']);
        $attributes['user_id'] = Auth::id();

        if ($question = $this->questionRepository->create($attributes)) {
            return redirect()->route('faq.show', $question->id)->with(SERVICE_RESPONSE_SUCCESS, __('Question has been created successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create question.'));
    }

}
