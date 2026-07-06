<?php

namespace App\Events\Exchange;

use App\Models\User\StockOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastCancelOrder implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $stockOrder;

    /**
     * Purpose: initializes the BroadcastCancelOrder instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * Create a new event instance.
     *
     * @param $stockOrder
     */
    public function __construct(StockOrder $stockOrder)
    {
        $this->stockOrder = $stockOrder;
    }

    /**
     * Purpose: returns the channels where the event is broadcast.
     *
     * Action: is used by Laravel broadcasting to deliver the event to the right subscribers.
     *
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): Channel
    {
        return new Channel(channel_prefix() .'orders.' . $this->stockOrder->stock_pair_id);
    }

    /**
     * Purpose: determines whether the event should be broadcast.
     *
     * Action: prevents unnecessary broadcasting when the event does not match the required business state.
     *
     * Determine if this event should broadcast.
     *
     * @return bool
     */
    public function broadcastWhen(): bool
    {
        return $this->stockOrder->category == CATEGORY_EXCHANGE;
    }

    /**
     * Purpose: returns the broadcast payload.
     *
     * Action: sends only the required event data to clients.
     *
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {

        return [
            'exchange_type' => $this->stockOrder->exchange_type,
            'order' => [
                'price' => $this->stockOrder->price,
                'amount' => bcmul($this->stockOrder->canceled, '-1'),
                'total' => bcmul(bcmul($this->stockOrder->price, $this->stockOrder->canceled), '-1')
            ]
        ];
    }
}
