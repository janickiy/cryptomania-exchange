<?php

namespace App\Listeners\Exchange;

use App\Events\Exchange\BroadcastOrder;
use App\Services\Exchange\StockExchangeService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessStopLimitStockOrderInQueue implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'stop-limit-order';

    /**
     * Purpose: initializes the ProcessStopLimitStockOrderInQueue instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Purpose: performs the main job or listener work.
     *
     * Action: handles a queued task or event outside the HTTP request lifecycle.
     *
     * Handle the event.
     *
     * @param BroadcastOrder $event
     * @return void
     */
    public function handle(BroadcastOrder $event): void
    {
        app(StockExchangeService::class, [$event->order])->process();
    }

    /**
     * Purpose: determines whether the listener should be queued.
     *
     * Action: controls asynchronous event processing.
     *
     */
    public function shouldQueue(mixed $event): bool
    {
        return (
            $event->order->status == STOCK_ORDER_PENDING &&
            $event->order->category == CATEGORY_EXCHANGE &&
            !is_null($event->order->stop_limit)
        );
    }
}
