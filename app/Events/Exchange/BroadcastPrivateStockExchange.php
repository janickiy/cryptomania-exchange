<?php

namespace App\Events\Exchange;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastPrivateStockExchange implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $stockPairId;
    public $userId;
    public $stockExchange;

    /**
     * Purpose: initializes the BroadcastPrivateStockExchange instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * Create a new event instance..' . $this->userId
     *
     * @param $stockPairId
     * @param $userId
     * @param $stockExchange
     */
    public function __construct(int|string $stockPairId, int|string $userId, array $stockExchange)
    {
        $this->stockPairId = $stockPairId;
        $this->userId = $userId;
        $this->stockExchange = $stockExchange;
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
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(channel_prefix() .'exchange.' . $this->stockPairId . '.' . $this->userId);
    }


    /**
     * Purpose: returns the broadcast payload.
     *
     * Action: sends only the required event data to clients.
     *
     */
    public function broadcastWith(): array
    {
        return $this->stockExchange;
    }
}
