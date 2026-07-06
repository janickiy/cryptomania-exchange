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
     * Purpose: initializes the OrdersController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly StockOrderService $stockOrderService)
    {
    }

    /**
     * Purpose: handles the open orders action in OrdersController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function openOrders(): View|Factory|Application
    {
        return view('frontend.orders.open_orders', [
            'list' => $this->stockOrderService->openOrders(),
            'title' => __('Open Orders'),
        ]);
    }

    /**
     * Purpose: creates a new record from request data.
     *
     * Action: passes validated data to the service layer and returns the operation result.
     *
     */
    public function store(StockOrderRequest $request): JsonResponse
    {
        $response = $this->stockOrderService->order($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return response()->json([$status => $response[SERVICE_RESPONSE_MESSAGE]]);
    }

    /**
     * Purpose: deletes the selected record.
     *
     * Action: performs deletion through a service or repository and redirects back with the result.
     *
     */
    public function destroy(int|string $id): JsonResponse
    {
        $response = $this->stockOrderService->cancelAuthenticatedOrder($id);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return response()->json([$status => $response[SERVICE_RESPONSE_MESSAGE]]);
    }
}
