<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class Permission
{

    /**
     * Purpose: handles an HTTP request in Permission middleware.
     *
     * Action: performs request checks or transformations before passing the request to the next handler.
     *
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $permission = has_permission($request->route()->getName(), null, false);
        if ($permission === true) {
            return $next($request);
        }
        throw new UnauthorizedException($permission, 401);
    }
}
