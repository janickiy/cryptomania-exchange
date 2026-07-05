<?php

namespace App\Events\Exchange;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastStockExchange implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payloadData;
    public $stockPairId;

    /**
     * Create a new event instance.
     *
     * @param $stockPairId
     * @param $payloadData
     */
    public function __construct(mixed $stockPairId, mixed $payloadData)
    {
        $this->stockPairId = $stockPairId;
        $this->payloadData = $payloadData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): Channel
    {
        return new Channel(channel_prefix() .'exchange.' . $this->stockPairId);
    }

    public function broadcastWith(): array
    {
        return $this->payloadData;
    }
}
