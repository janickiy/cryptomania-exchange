<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Services\Core\DataListService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела уведомлений пользователя.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly NotificationInterface $notification,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела уведомлений пользователя.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
    {
        $user = Auth::user();
        $data['title'] = __('Notices');

        $searchFields = [
            ['data', __('Notice')],
        ];

        $orderFields = [
            ['id', __('Serial')],
            ['data', __('Notice')],
            ['created_at', __('Date')],
            ['read_at', __('Status')],
        ];

        $where = ['user_id' => $user->id];
        $query = $this->notification->paginateWithFilters($searchFields, $orderFields, $where);
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);

        return view('backend.notices.index', $data);
    }

    /**
     * Назначение: помечает уведомление прочитанным.
     *
     * Действие: обновляет статус уведомления по идентификатору и возвращает пользователя назад.
     */
    public function markAsRead(int|string $id): RedirectResponse
    {
        if ($this->notification->read($id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The notice has been marked as read.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to mark as read.'));
    }

    /**
     * Назначение: помечает уведомление непрочитанным.
     *
     * Действие: обновляет статус уведомления по идентификатору и возвращает пользователя назад.
     */
    public function markAsUnread(int|string $id): RedirectResponse
    {
        if ($this->notification->unread($id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The notice has been marked as unread.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to mark as unread.'));
    }
}
