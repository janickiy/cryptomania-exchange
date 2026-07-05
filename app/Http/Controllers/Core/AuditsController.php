<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Repositories\Core\Interfaces\AuditInterface;
use App\Services\Core\DataListService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class AuditsController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела аудита действий пользователей.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly AuditInterface $audit,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела аудита действий пользователей.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
    {
        $searchFields = [
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
            ['email', __('Email')],
            ['event', __('Event')],
        ];
        $orderFields = [
            ['id', __('Serial')],
            ['email', __('Email')],
            ['created_ar', __('Date')],
        ];

        $query = $this->audit->paginateWithUserFilters($searchFields, $orderFields);
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Audits');

        return view('backend.audits.index', $data);
    }
}
