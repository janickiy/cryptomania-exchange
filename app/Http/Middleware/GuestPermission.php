<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class GuestPermission
{
    /**
     * Purpose: handles an HTTP request in GuestPermission middleware.
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

        throw new UnauthorizedException(ROUTE_REDIRECT_TO_UNAUTHORIZED,401);
    }
}
