<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class VerificationPermission
{
    /**
     * Purpose: handles an HTTP request in VerificationPermission middleware.
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
        $auth = Auth::user();
        if (
            (!$auth || ($auth && $auth->is_email_verified == EMAIL_VERIFICATION_STATUS_INACTIVE)) &&
            admin_settings('require_email_verification') == ACTIVE_STATUS_ACTIVE
        ) {
            return $next($request);
        }
        throw new UnauthorizedException(ROUTE_REDIRECT_TO_UNAUTHORIZED, 401);
    }
}
