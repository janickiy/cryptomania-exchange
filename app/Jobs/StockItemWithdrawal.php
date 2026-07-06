<?php

namespace App\Jobs;

use App\Services\User\Trader\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StockItemWithdrawal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $withdrawalId;
    public $address;
    public $amount;

    /**
     * Purpose: initializes the StockItemWithdrawal instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * Create a new job instance.
     *
     * @param $withdrawalId
     */
    public function __construct(int|string $withdrawalId)
    {
        $this->queue = 'withdrawal';
        $this->withdrawalId = $withdrawalId;
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
        app(WalletService::class)->send($this->withdrawalId);
    }
}
