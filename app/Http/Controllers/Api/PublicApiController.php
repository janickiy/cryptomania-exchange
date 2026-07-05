<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\PublicApiRequest;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Http\Controllers\Controller;
use App\Services\Exchange\StockGraphDataService;
use Illuminate\Http\JsonResponse;

class PublicApiController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела публичного API.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly StockPairInterface $stockPairs,
        private readonly StockGraphDataService $stockGraphDataService,
    ) {
    }

    /**
     * Назначение: выполняет команду публичного API.
     *
     * Действие: валидирует API-запрос, передает команду сервису и возвращает сформированный ответ.
     */
    public function command(PublicApiRequest $request): mixed
    {
        $command = $request->get('command');

        return $this->{$command}($request);
    }

    /**
     * Назначение: возвращает ticker-данные публичного API.
     *
     * Действие: получает рыночные данные по запросу и отдает их в формате API-ответа.
     */
    public function returnTicker(PublicApiRequest $request): mixed
    {
        $conditions = ['stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE];
        if ($request->has('coinPair')) {
            $coinPair = explode('_', $request->get('coinPair'));
            $conditions['stock_item.item'] = strtoupper($coinPair[0]);
            $conditions['base_item.item'] = strtoupper($coinPair[1]);
        }

        $response = $this->stockPairs->getAllStockPairForApiByConditions($conditions);

        if (empty($response)) {
            return response()->json('No coin pair found.');
        }

        return $response;
    }

    /**
     * Назначение: возвращает данные графика для публичного API.
     *
     * Действие: читает параметры графика, получает свечи или точки графика и возвращает JSON.
     */
    public function returnChartData(PublicApiRequest $request): JsonResponse
    {
        $coinPair = explode('_', $request->get('coinPair'));
        $interval = $request->get('interval');

        $stockPair = $this->stockPairs->getByPair(strtoupper($coinPair[0]), strtoupper($coinPair[1]));

        if (empty($stockPair)) {
            return response()->json('Invalid coin pair.');
        }

        $chartData = $this->stockGraphDataService->getGraphData($stockPair->id, $interval);

        $refactoredData = [];

        foreach ($chartData as $data) {
            $refactoredData[] = [
                'date' => $data[0],
                'open' => $data[1],
                'close' => $data[4],
                'high' => $data[2],
                'low' => $data[3],
            ];
        }

        return response()->json($refactoredData);
    }
}
