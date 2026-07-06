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
     * Purpose: initializes the PublicApiController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly StockPairInterface $stockPairs,
        private readonly StockGraphDataService $stockGraphDataService,
    ) {
    }


    /**
     * Purpose: handles the command action in PublicApiController.
     * Action: connects the HTTP request to services or views so the controller remains th
     *
     * @param PublicApiRequest $request
     * @return JsonResponse|array
     */
    public function command(PublicApiRequest $request): JsonResponse|array
    {
        $command = $request->input('command');

        return $this->{$command}($request);
    }

    /**
     * Purpose: handles the return ticker action in PublicApiController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function returnTicker(PublicApiRequest $request): JsonResponse|array
    {
        $conditions = ['stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE];
        if ($request->has('coinPair')) {
            $coinPair = explode('_', $request->input('coinPair'));
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
     * Purpose: handles the return chart data action in PublicApiController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     * @param PublicApiRequest $request
     * @return JsonResponse
     */
    public function returnChartData(PublicApiRequest $request): JsonResponse
    {
        $coinPair = explode('_', $request->input('coinPair'));
        $interval = $request->input('interval');

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
