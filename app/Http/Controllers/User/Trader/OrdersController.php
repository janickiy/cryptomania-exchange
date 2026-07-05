<?php

namespace App\Http\Controllers\User\Trader;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Trader\StockOrderRequest;
use App\Services\User\Trader\StockOrderService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;

class OrdersController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела биржевых ордеров.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(private readonly StockOrderService $stockOrderService)
    {
    }

    /**
     * Назначение: показывает открытые ордера пользователя.
     *
     * Действие: запрашивает текущие открытые ордера и возвращает представление списка.
     */
    public function openOrders(): View|Factory|Application
    {
        return view('frontend.orders.open_orders', [
            'list' => $this->stockOrderService->openOrders(),
            'title' => __('Open Orders'),
        ]);
    }

    /**
     * Назначение: создает новую запись в разделе биржевых ордеров.
     *
     * Действие: принимает валидированный запрос, передает данные в сервис или репозиторий и возвращает результат операции.
     */
    public function store(StockOrderRequest $request): JsonResponse
    {
        $response = $this->stockOrderService->order($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return response()->json([$status => $response[SERVICE_RESPONSE_MESSAGE]]);
    }

    /**
     * Назначение: удаляет запись в разделе биржевых ордеров.
     *
     * Действие: проверяет возможность удаления, выполняет операцию через сервис или репозиторий и возвращает результат.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $response = $this->stockOrderService->cancelAuthenticatedOrder($id);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return response()->json([$status => $response[SERVICE_RESPONSE_MESSAGE]]);
    }
}
