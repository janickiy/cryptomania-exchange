<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GuestPermissionApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(mixed $request, Closure $next): Response
    {
        $auth = Auth::user();
        if(!$auth) {
            return $next($request);
        }
        return response()->json([401 =>api_permission(ROUTE_REDIRECT_TO_UNAUTHORIZED)]);
    }
}
