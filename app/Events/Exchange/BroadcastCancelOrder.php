<?php

namespace App\Events\Exchange;

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
     * Create a new event instance.
     *
     * @param $stockOrder
     */
    public function __construct(mixed $stockOrder)
    {
        $this->stockOrder = $stockOrder;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): Channel
    {
        return new Channel(channel_prefix() .'orders.' . $this->stockOrder->stock_pair_id);
    }

    /**
     * Determine if this event should broadcast.
     *
     * @return bool
     */
    public function broadcastWhen(): bool
    {
        return $this->stockOrder->category == CATEGORY_EXCHANGE;
    }

    /**
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
