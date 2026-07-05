<?php

namespace App\Http\Controllers\Core;

use App\DTO\Admin\SystemNoticeData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SystemNoticeRequest;
use App\Repositories\Core\Interfaces\SystemNoticeInterface;
use App\Services\Core\DataListService;
use App\Services\User\Admin\SystemNoticeAdminService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class SystemNoticeController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела системных уведомлений.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly SystemNoticeInterface $systemNotice,
        private readonly SystemNoticeAdminService $systemNoticeService,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела системных уведомлений.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
    {
        $searchFields = [
            ['title', __('Title')],
        ];
        $orderFields = [
            ['id', __('Serial')],
            ['type', __('Type')],
            ['status', __('Status')],
            ['start_at', __('Start Time')],
            ['end_at', __('End Time')],
        ];

        $query = $this->systemNotice->paginateWithFilters($searchFields, $orderFields);
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('System Notice');

        return view('backend.systemNotice.index', $data);
    }

    /**
     * Назначение: показывает форму создания записи в разделе системных уведомлений.
     *
     * Действие: подготавливает справочные данные для формы и возвращает представление создания.
     */
    public function create(): View|Factory|Application
    {
        $data['types'] = array_combine(config('commonconfig.system_notice_types'), array_map('ucfirst', config('commonconfig.system_notice_types')));
        $data['title'] = __('Create Notice');

        return view('backend.systemNotice.create', $data);
    }

    /**
     * Назначение: создает новую запись в разделе системных уведомлений.
     *
     * Действие: принимает валидированный запрос, передает данные в сервис или репозиторий и возвращает результат операции.
     */
    public function store(SystemNoticeRequest $request): RedirectResponse
    {
        if ($this->systemNoticeService->create(SystemNoticeData::fromArray($request->validated()))) {
            return redirect()->route('system-notices.index')->with(SERVICE_RESPONSE_SUCCESS, __('Notice has been created successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create notice.'));
    }

    /**
     * Назначение: показывает форму редактирования записи в разделе системных уведомлений.
     *
     * Действие: загружает запись и справочные данные, затем возвращает представление формы редактирования.
     */
    public function edit(int|string $id): View|Factory|Application
    {
        $data['systemNotice'] = $this->systemNotice->findOrFailById($id);
        $data['types'] = array_combine(config('commonconfig.system_notice_types'), array_map('ucfirst', config('commonconfig.system_notice_types')));
        $data['title'] = __('Edit Notices');

        return view('backend.systemNotice.edit', $data);
    }

    /**
     * Назначение: обновляет запись в разделе системных уведомлений.
     *
     * Действие: принимает валидированный запрос, передает изменения в сервис или репозиторий и возвращает ответ с результатом.
     */
    public function update(SystemNoticeRequest $request, int|string $id): RedirectResponse
    {
        if ($this->systemNoticeService->update((int) $id, SystemNoticeData::fromArray($request->validated()))) {
            return redirect()->route('system-notices.index')->with(SERVICE_RESPONSE_SUCCESS, __('System notice has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update system notice.'));
    }

    /**
     * Назначение: удаляет запись в разделе системных уведомлений.
     *
     * Действие: проверяет возможность удаления, выполняет операцию через сервис или репозиторий и возвращает результат.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        if ($this->systemNoticeService->delete((int) $id)) {
            return redirect()->route('system-notices.index')->with(SERVICE_RESPONSE_SUCCESS, __('System notice has been deleted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete system notice.'));
    }
}
