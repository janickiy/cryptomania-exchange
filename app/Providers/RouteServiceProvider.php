<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot(): void
    {
        Route::pattern('id', '[0-9]+');
        Route::pattern('admin_setting_type', implode('|', array_keys(config('adminsetting.settings'))));
        Route::pattern('menu_slug', implode('|', config('navigation.registered_place')));
        Route::pattern('paymentTransactionType', implode('|', array_keys(config('commonconfig.payment_slug'))));
        Route::pattern('categoryType', implode('|', array_keys(config('commonconfig.category_slug'))));
        Route::pattern('journalType', implode('|', array_keys(config('commonconfig.journal_type'))));
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mappermissionApiRoutes();
        $this->mapGuestpermissionApiRoutes();
        $this->mapVerificationpermissionApiRoutes();

        $this->mapWebRoutes();
        $this->mapPermissionRoutes();
        $this->mapGuestPermissionRoutes();
        $this->mapVerificationPermissionRoutes();

        $this->exchnageRoute();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    /*protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }*/

    protected function mapWebRoutes(): void
    {
        $filename = $middleware = 'web';
        $prefix = $namespace = null;
        $this->routeMap($filename, $middleware, $prefix, $namespace, 'routes/');
    }

    protected function routeMap(mixed $filename, mixed $middleware, mixed $prefix = null, mixed $namespace = null, mixed $path = 'routes/groups/'): void
    {
        $locale = strtolower($this->app->request->segment(1));
        $language = check_language($locale);
        if ($language != null && $prefix != null) {
            $prefix = $language . '/' . $prefix;
        } elseif ($language != null) {
            $prefix = $language;
        }

        if ($namespace != null) {
            $namespace = $this->namespace . '\\' . ucfirst($namespace);
        } else {
            $namespace = $this->namespace;
        }

        Route::prefix($prefix)
            ->middleware($middleware)
            ->namespace($namespace)
            ->group(base_path($path . $filename . '.php'));
    }

    protected function mapPermissionRoutes(): void
    {
        $filename = 'permission';
        $middleware = ['web', 'auth', '2fa', 'permission'];
        $prefix = $namespace = null;
        $this->routeMap($filename, $middleware, $prefix, $namespace);
    }

    protected function mapGuestPermissionRoutes(): void
    {
        $filename = 'guest_permission';
        $middleware = ['web', 'guest.permission'];
        $prefix = $namespace = null;
        $this->routeMap($filename, $middleware, $prefix, $namespace);
    }

    protected function mapVerificationPermissionRoutes(): void
    {
        $filename = 'verification_permission';
        $middleware = ['web', 'verification.permission'];
        $prefix = $namespace = null;
        $this->routeMap($filename, $middleware, $prefix, $namespace);
    }
    // API Starts here


    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mappermissionApiRoutes(): void
    {
        $filename = 'permission_api';
        $middleware = ['api', 'auth', 'permission.api'];
        $prefix = $namespace = null;
        $this->routeMap($filename, $middleware, $prefix, $namespace);
    }

    protected function mapGuestpermissionApiRoutes(): void
    {
        $filename = 'guest_permission_api';
        $middleware = ['api', 'guest.permission.api'];
        $prefix = $namespace = null;
        $this->routeMap($filename, $middleware, $prefix, $namespace);
    }

    protected function mapVerificationpermissionApiRoutes(): void
    {
        $filename = 'verification_permission_api';
        $middleware = ['api', 'verification.permission.api'];
        $prefix = $namespace = null;
        $this->routeMap($filename, $middleware, $prefix, $namespace);
    }

    private function exchnageRoute(): void
    {
        $filename = 'exchange';
        $middleware = ['web'];
        $prefix = 'exchange';
        $namespace = 'Exchange';
        $this->routeMap($filename, $middleware, $prefix, $namespace);
    }
}
