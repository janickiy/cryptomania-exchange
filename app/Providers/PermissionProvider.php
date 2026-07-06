<?php

namespace App\Providers;

use App\Repositories\Core\Interfaces\AdminSettingInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class PermissionProvider extends ServiceProvider
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
        $this->loadAdminSettings();
    }

    /**
     * Purpose: configures the provider through load admin settings.
     *
     * Action: is used by Laravel during application startup to prepare infrastructure.
     *
     */
    private function loadAdminSettings(): void
    {
        $adminSettings = admin_settings();
        if (empty($adminSettings)) {
            try {
                $adminSettings = $this->app->make(AdminSettingInterface::class)->getAll();
                $adminSettings = $adminSettings->pluck('value', 'slug')->toArray();
                foreach ($adminSettings as $key => $val) {
                    if (is_json($val)) {
                        $adminSettings[$key] = json_decode($val, true);
                    }
                }
            } catch (\Exception $e) {
                $adminSettings = ['lang' => LANGUAGE_DEFAULT];
            }
            Cache::forever('admin_settings', $adminSettings);
        }
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
