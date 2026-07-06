<?php

namespace App\Providers;

use App\Models\Core\SystemNotice;
use App\Models\User\UserInfo;
use App\Observers\Core\SystemNoticeObserver;
use App\Observers\User\UserInfoObserver;
use Illuminate\Support\ServiceProvider;

class ObserverProvider extends ServiceProvider
{
    /**
     * Purpose: runs boot-time provider configuration.
     *
     * Action: connects routes, observers, policies, or other settings after services are registered.
     *
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        UserInfo::observe(UserInfoObserver::class);
        SystemNotice::observe(SystemNoticeObserver::class);
    }

    /**
     * Purpose: registers application dependencies in the service container.
     *
     * Action: prepares bindings used later by Laravel and application layers.
     *
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }
}
