<?php

namespace App\Events\Exchange;

use App\Models\User\StockOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastOrder implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Purpose: initializes the BroadcastOrder instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * Create a new event instance.
     *
     * @param $order
     */
    public function __construct(StockOrder $order)
    {
        $this->order = $order;
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
        return new Channel(channel_prefix() .'orders.' . $this->order->stock_pair_id);
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
        return ($this->order->status == STOCK_ORDER_PENDING && $this->order->category == CATEGORY_EXCHANGE);
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
            'exchange_type' => $this->order->exchange_type,
            'order' => [
                'price' => $this->order->price,
                'amount' => $this->order->amount,
                'total' => bcmul($this->order->price, $this->order->amount)
            ]
        ];
    }
}
