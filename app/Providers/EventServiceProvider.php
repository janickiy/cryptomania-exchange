<?php

namespace App\Providers;

use App\Events\Exchange\BroadcastOrder;
use App\Listeners\Exchange\ProcessStockOrderInQueue;
use App\Listeners\Exchange\ProcessStopLimitStockOrderInQueue;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        BroadcastOrder::class => [
            ProcessStockOrderInQueue::class,
            ProcessStopLimitStockOrderInQueue::class
        ],
    ];

    /**
     * Purpose: runs boot-time provider configuration.
     *
     * Action: connects routes, observers, policies, or other settings after services are registered.
     *
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        //
    }
}
