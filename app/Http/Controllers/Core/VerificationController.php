<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\PasswordResetRequest;
use App\Services\Core\VerificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Purpose: initializes the VerificationController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly VerificationService $verificationService)
    {
    }

    /**
     * Purpose: handles user or account verification.
     *
     * Action: checks the provided parameters and redirects with the verification result
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function verify(Request $request): RedirectResponse
    {
        $response = $this->verificationService->verifyUserEmail($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;
        $route = Auth::check() ? REDIRECT_ROUTE_TO_USER_AFTER_LOGIN : REDIRECT_ROUTE_TO_LOGIN;

        return redirect()->route($route)->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: shows the form for resending a verification message.
     *
     * Action: returns the view where the user can request a new email.
     *
     * @return View
     */
    public function resendForm(): View
    {
        return view('backend.email_verify');
    }

    /**
     * Purpose: sends a system email or notification from request data.
     *
     * Action: delegates sending to a service and returns the result to the user.
     *
     * @param PasswordResetRequest $request
     * @return RedirectResponse
     */
    public function send(PasswordResetRequest $request): RedirectResponse
    {
        $response = $this->verificationService->sendVerificationLink($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
