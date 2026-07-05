<?php

namespace App\Http\Controllers\TradingView;

use App\Models\User\Question;
use App\Repositories\User\Trader\Interfaces\QuestionInterface;
use App\Services\Core\DataListService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела часто задаваемых вопросов.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly QuestionInterface $questions,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела часто задаваемых вопросов.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
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
     * Назначение: показывает детальную страницу записи в разделе часто задаваемых вопросов.
     *
     * Действие: находит запись по идентификатору, подготавливает связанные данные и возвращает представление просмотра.
     */
    public function show(int|string $id): View|Factory|Application
    {
        $data['question'] = $this->questions->findOrFailByConditions(['id' => $id]);
        $data['title'] = __('Question Details');
        return view('frontend.faq.show', $data);
    }
}
