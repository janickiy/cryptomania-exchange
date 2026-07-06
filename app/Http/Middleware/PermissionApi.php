<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionApi
{
    /**
     * Purpose: handles an HTTP request in PermissionApi middleware.
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
        $permission = has_permission($request->route()->getName(), null, false, true);
        if ($permission === true) {
            return $next($request);
        }
        return response()->json([401 => api_permission($permission)]);
    }
}
