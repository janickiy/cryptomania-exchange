<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationPermission
{
    /**
     * Purpose: handles an HTTP request in RegistrationPermission middleware.
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
        if (admin_settings('registration_active_status') != ACTIVE_STATUS_ACTIVE) {
            abort(404, __('Registration is currently disabled.'));
        }

        return $next($request);
    }
}
