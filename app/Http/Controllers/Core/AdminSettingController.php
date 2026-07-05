<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminSettingRequest;
use App\Services\Core\AdminSettingService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class AdminSettingController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела административных настроек.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(private readonly AdminSettingService $adminSettingService)
    {
    }

    /**
     * Назначение: показывает основную страницу или список раздела административных настроек.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(string $adminSettingType = 'general'): View|Factory|Application
    {
        $data['settings'] = $this->adminSettingService->adminForm($adminSettingType, true);
        $data['adminSettingType'] = $adminSettingType;
        $data['title'] = __('Admin Setting');

        return view('backend.adminSetting.index', $data);
    }

    /**
     * Назначение: показывает форму редактирования записи в разделе административных настроек.
     *
     * Действие: загружает запись и справочные данные, затем возвращает представление формы редактирования.
     */
    public function edit(string $adminSettingType): View|Factory|Application
    {
        $data['settings'] = $this->adminSettingService->adminForm($adminSettingType);
        $data['adminSettingType'] = $adminSettingType;
        $data['title'] = __('Edit Admin Setting');

        return view('backend.adminSetting.edit', $data);
    }

    /**
     * Назначение: обновляет запись в разделе административных настроек.
     *
     * Действие: принимает валидированный запрос, передает изменения в сервис или репозиторий и возвращает ответ с результатом.
     */
    public function update(AdminSettingRequest $request, string $adminSettingType): RedirectResponse
    {
        $response = $this->adminSettingService->adminUpdate($request, $adminSettingType);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->route('admin-settings.edit', $adminSettingType)->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
