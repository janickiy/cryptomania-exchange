<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\UnauthorizedException;

class Permission
{

    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(mixed $request, Closure $next): mixed
    {
        $permission = has_permission($request->route()->getName(), null, false);
        if ($permission === true) {
            return $next($request);
        }
        throw new UnauthorizedException($permission, 401);
    }
}
