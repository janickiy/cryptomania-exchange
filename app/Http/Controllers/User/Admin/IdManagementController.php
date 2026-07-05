<?php

namespace App\Http\Controllers\User\Admin;

use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Http\Controllers\Controller;
use App\Services\Core\DataListService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class IdManagementController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела администрирования документов пользователей.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly UserInfoInterface $userInfo,
        private readonly NotificationInterface $notifications,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела администрирования документов пользователей.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
    {
        $searchFields = [
            ['email', __('Email')],
        ];

        $orderFields = [
            ['email', __('Email')],
        ];

        $joinArray = ['users', 'users.id', '=', 'user_infos.user_id'];

        $select = ['users.id as id', 'email', 'id_type', 'is_id_verified'];
        $query = $this->userInfo->paginateWithFilters($searchFields, $orderFields, ['is_id_verified', '!=', ID_STATUS_UNVERIFIED], $select, $joinArray);
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('ID Management');

        return view('backend.idManagement.index', $data);
    }

    /**
     * Назначение: показывает детальную страницу записи в разделе администрирования документов пользователей.
     *
     * Действие: находит запись по идентификатору, подготавливает связанные данные и возвращает представление просмотра.
     */
    public function show(int|string $id): View|Factory|Application
    {
        $where = ['user_id'=> $id, ['is_id_verified', '!=', ID_STATUS_UNVERIFIED]];
        $data['user'] = $this->userInfo->findOrFailByConditions($where, ['user']);
        $data['title'] = __('View ID Verification Request');

        return view('backend.idManagement.show', $data);
    }

    /**
     * Назначение: подтверждает запись в разделе администрирования документов пользователей.
     *
     * Действие: передает идентификатор в сервис проверки, меняет статус записи и возвращает результат.
     */
    public function approve(int|string $id): RedirectResponse {
        try {
            $conditions = ['user_id'=> $id, 'is_id_verified' => ID_STATUS_PENDING];
            $attributes = ['is_id_verified' => ID_STATUS_VERIFIED];

            if (!$this->userInfo->updateByConditions($attributes, $conditions)) {
                throw new \Exception('Failed to approve.');
            }

            $notification = ['user_id' => $id, 'data' => __("Your ID verification request has been approved.")];
            $this->notifications->create($notification);

            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The ID has been approved successfully.'));
        }
        catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to approve.'));
        }
    }

    /**
     * Назначение: отклоняет запись в разделе администрирования документов пользователей.
     *
     * Действие: передает идентификатор в сервис проверки, меняет статус записи и возвращает результат.
     */
    public function decline(int|string $id): RedirectResponse
    {
        try {
            $attributes = [
                'is_id_verified' => ID_STATUS_UNVERIFIED,
                'id_type' => null,
                'id_card_front' => null,
                'id_card_back' => null,
            ];

            $conditions = ['user_id'=> $id, 'is_id_verified' => ID_STATUS_PENDING];

            if (!$this->userInfo->updateByConditions($attributes, $conditions)) {
                throw new \Exception('Failed to decline.');
            }

            $notification = ['user_id' => $id, 'data' => __("Your ID verification request has been declined.")];
            $this->notifications->create($notification);

            return redirect()->route('admin.id-management.index')->with(SERVICE_RESPONSE_SUCCESS, __('The ID has been declined successfully.'));
        }
        catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to decline.'));
        }
    }
}
