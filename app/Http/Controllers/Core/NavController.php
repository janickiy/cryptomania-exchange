<?php

namespace App\Http\Controllers\Core;

use App\Services\Core\NavService;
use App\Http\Requests\Admin\NavRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class NavController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела навигационных настроек.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(private readonly NavService $navService)
    {
    }

    /**
     * Назначение: показывает основную страницу или список раздела навигационных настроек.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(?string $slug = null): View|Factory|Application
    {
        $data = $this->navService->backendMenuBuilder($slug);
        $data['title'] = __('Navigation');

        return view('backend.navigation.index', $data);
    }

    /**
     * Назначение: обрабатывает действие `save` в разделе навигационных настроек.
     *
     * Действие: координирует получение данных, вызов сервисного слоя и возврат ответа пользователю.
     */
    public function save(NavRequest $request, string $slug): RedirectResponse
    {
        $response = $this->navService->backendMenuSave($request, $slug);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
