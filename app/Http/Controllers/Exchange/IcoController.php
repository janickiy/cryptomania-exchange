<?php

namespace App\Http\Controllers\Exchange;

use App\DTO\Exchange\IcoPurchaseData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Trader\IcoStoreRequest;
use App\Services\Exchange\IcoService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class IcoController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела ICO-покупок.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(private readonly IcoService $icoService)
    {
    }

    /**
     * Назначение: показывает основную страницу или список раздела ICO-покупок.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
    {
        return view('frontend.ico.index', $this->icoService->indexData());
    }

    /**
     * Назначение: показывает форму покупки ICO-актива.
     *
     * Действие: загружает данные выбранного ICO-актива и возвращает страницу покупки.
     */
    public function buy(int|string $id): View|Factory|Application
    {
        return view('frontend.ico.buy', $this->icoService->buyData($id));
    }

    /**
     * Назначение: создает новую запись в разделе ICO-покупок.
     *
     * Действие: принимает валидированный запрос, передает данные в сервис или репозиторий и возвращает результат операции.
     */
    public function store(IcoStoreRequest $request): RedirectResponse
    {
        $response = $this->icoService->purchase(IcoPurchaseData::fromArray($request->validated()));
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
