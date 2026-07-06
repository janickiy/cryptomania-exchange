<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MaintenancePermissionApi
{
    /**
     * Purpose: handles an HTTP request in MaintenancePermissionApi middleware.
     *
     * Action: performs request checks or transformations before passing the request to the next handler.
     *
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth = Auth::check();
        $is_under_maintenance = admin_settings('maintenance_mode');
        $route_name = $request->route()->getName();
        $avoidable_maintenance_routes = config('routeApi.' . ROUTE_TYPE_AVOIDABLE_MAINTENANCE);

        if ($is_under_maintenance == UNDER_MAINTENANCE_MODE_ACTIVE && !$auth && !in_array($route_name, $avoidable_maintenance_routes)) {
            return response()->json([401 => api_permission(ROUTE_REDIRECT_TO_UNDER_MAINTENANCE)]);
        }

        return $next($request);
    }
}
