<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class RegistrationPermission
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(mixed $request, Closure $next): Response
    {
        if (admin_settings('registration_active_status') != ACTIVE_STATUS_ACTIVE) {
            abort(404, __('Registration is currently disabled.'));
        }

        return $next($request);
    }
}
