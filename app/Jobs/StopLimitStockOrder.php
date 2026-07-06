<?php

namespace App\Jobs;

use App\Events\Exchange\BroadcastOrder;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StopLimitStockOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $stockPairId;
    public $stockPrice;

    /**
     * Purpose: initializes the StopLimitStockOrder instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * Create a new job instance.
     *
     * @param $stockPairId
     * @param $stockPrice
     */
    public function __construct(int|string $stockPairId, int|float|string $stockPrice)
    {
        $this->queue = 'stop-limit';
        $this->stockPairId = $stockPairId;
        $this->stockPrice = $stockPrice;
    }

    /**
     * Purpose: performs the main job or listener work.
     *
     * Action: handles a queued task or event outside the HTTP request lifecycle.
     *
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $conditions = [
            'category' => CATEGORY_EXCHANGE,
            'stock_pair_id' => $this->stockPairId,
            'status' => STOCK_ORDER_INACTIVE,
            ['stop_limit', '!=', null],
        ];

        $stockOrder = app(StockOrderInterface::class);
        $stopLimitStockOrders = $stockOrder->getStopLimitOrders($conditions, $this->stockPrice);

        if (!$stopLimitStockOrders->isEmpty()) {
            $activeStopLimitStockOrderInputs = [];
            foreach ($stopLimitStockOrders as $limitStockOrder) {
                array_push($activeStopLimitStockOrderInputs, [
                    'conditions' => ['id' => $limitStockOrder->id],
                    'fields' => [
                        'status' => STOCK_ORDER_PENDING
                    ]
                ]);
            }

            $updateStockOrderCount = $stockOrder->bulkUpdate($activeStopLimitStockOrderInputs);

            if ($stopLimitStockOrders->count() == $updateStockOrderCount) {
                $stopLimitStockOrders = $stockOrder->getStopLimitOrdersByIds($stopLimitStockOrders->pluck('id')->toArray());

                foreach ($stopLimitStockOrders as $stopLimitStockOrder) {
                    event(new BroadcastOrder($stopLimitStockOrder));
                }
            }
        }
    }
}
