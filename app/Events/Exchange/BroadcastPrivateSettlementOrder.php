<?php

namespace App\Events\Exchange;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastPrivateSettlementOrder implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $stockPairId;
    public $userId;
    public $orders;

    /**
     * Create a new event instance.
     *
     * @param $stockPairId
     * @param $userId
     * @param $orders
     */
    public function __construct(mixed $stockPairId, mixed $userId, mixed $orders)
    {
        $this->stockPairId = $stockPairId;
        $this->userId = $userId;
        $this->orders = $orders;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(channel_prefix() .'exchange.' . $this->stockPairId . '.' . $this->userId);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return $this->orders;
    }
}
