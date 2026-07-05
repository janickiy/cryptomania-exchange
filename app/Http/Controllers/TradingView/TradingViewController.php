<?php

namespace App\Http\Controllers\TradingView;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Admin\CommentRequest;
use App\Models\Backend\Post;
use App\Repositories\User\Interfaces\CommentInterface;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;
use App\Services\Core\DataListService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TradingViewController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела торговых обзоров.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly PostInterface $posts,
        private readonly CommentInterface $comments,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела торговых обзоров.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
    {
        $searchFields = [
            ['posts.title', __('Title')],
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
        ];

        $orderFields = [
            ['posts.title', __('Title')],
            ['posts.created_at', __('Date')],
        ];
        $where = ['is_published' => ACTIVE_STATUS_ACTIVE];
        $groupBy = ['posts.id', 'posts.title', 'posts.content', 'posts.featured_image', 'posts.created_at', 'users.avatar', 'first_name', 'last_name'];

        $select = ['posts.id', 'posts.title', 'posts.content', 'posts.featured_image', 'posts.created_at', 'users.avatar', 'first_name', 'last_name', DB::raw('count(comments.id) as comments')];
        $joinArray = [
            ['users', 'users.id', '=', 'posts.user_id'],
            ['user_infos', 'users.id', '=', 'user_infos.user_id'],
            ['comments', 'comments.commentable_id', '=', 'posts.id', ['commentable_type' => get_class(new Post())]
            ],
        ];

        $query = $this->posts->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray, $groupBy, 6);
        $data['posts'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Trading Views');
        return view('frontend.trade_analysis.lists', $data);
    }

    /**
     * Назначение: показывает детальную страницу записи в разделе торговых обзоров.
     *
     * Действие: находит запись по идентификатору, подготавливает связанные данные и возвращает представление просмотра.
     */
    public function show(int|string $id): View|Factory|Application
    {
        $data['post'] = $this->posts->findOrFailByConditions(['id' => $id, 'is_published' => ACTIVE_STATUS_ACTIVE]);
        $data['title'] = __('Trading View');
        return view('frontend.trade_analysis.show', $data);
    }

    /**
     * Назначение: сохраняет комментарий к торговому обзору.
     *
     * Действие: принимает валидированный текст комментария, связывает его с публикацией и возвращает пользователя назад.
     */
    public function comment(CommentRequest $request, int|string $id): RedirectResponse
    {
        $post = $this->posts->getFirstById((int) $id);

        if (empty($post)) {
            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Post could not found.'));
        }

        $attributes = $request->only('content');
        $attributes['user_id'] = Auth::id();


        if ($this->comments->save($attributes, $post)) {
            return redirect()->route('trading-views.show', $post->id)->with(SERVICE_RESPONSE_SUCCESS, __('Comment has been submitted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to place comment.'));
    }
}
