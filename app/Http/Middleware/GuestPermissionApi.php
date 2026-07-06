<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GuestPermissionApi
{
    /**
     * Purpose: handles an HTTP request in GuestPermissionApi middleware.
     *
     * Action: performs request checks or transformations before passing the request to the next handler.
     *
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth = Auth::user();
        if(!$auth) {
            return $next($request);
        }
        return response()->json([401 =>api_permission(ROUTE_REDIRECT_TO_UNAUTHORIZED)]);
    }
}
