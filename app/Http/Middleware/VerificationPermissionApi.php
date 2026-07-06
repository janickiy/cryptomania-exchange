<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificationPermissionApi
{
    /**
     * Purpose: handles an HTTP request in VerificationPermissionApi middleware.
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
        return response()->json([401 => api_permission(ROUTE_REDIRECT_TO_UNAUTHORIZED)]);
    }
}
