<?php

namespace App\Events\Exchange;

use App\Models\User\StockOrder;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastPrivateCancelOrder implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stockOrder;

    /**
     * Purpose: initializes the BroadcastPrivateCancelOrder instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * Create a new event instance.
     *
     * @param $order
     */
    public function __construct(StockOrder $order)
    {
        $this->stockOrder = $order;
    }

    /**
     * Purpose: returns the channels where the event is broadcast.
     *
     * Action: is used by Laravel broadcasting to deliver the event to the right subscribers.
     *
     * Get the channels the event should broadcast on.
     *
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(channel_prefix() .'orders.' . $this->stockOrder->stock_pair_id . '.' . $this->stockOrder->user_id);
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
            'order_number' => $this->stockOrder->id,
            'exchange_type' => $this->stockOrder->exchange_type,
            'price' => $this->stockOrder->price,
            'amount' => $this->stockOrder->canceled,
            'total' => bcmul($this->stockOrder->canceled, $this->stockOrder->price)
        ];
    }
}
