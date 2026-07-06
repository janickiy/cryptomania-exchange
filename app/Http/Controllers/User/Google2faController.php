<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\Google2faRequest;
use App\Http\Controllers\Controller;
use App\Services\User\Google2faService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class Google2faController extends Controller
{
    /**
     * Purpose: initializes the Google2faController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly Google2faService $google2faService,
    ) {
    }

    /**
     * Purpose: displays the Google 2FA management page.
     *
     * Action: delegates profile, secret, and QR-code data preparation to the Google 2FA service.
     *
     */
    public function create(): View|Factory|Application
    {
        return view('backend.google2fa.create', $this->google2faService->createData());
    }


    /**
     * Purpose: enables Google 2FA for the authenticated user.
     *
     * Action: delegates OTP verification and secret persistence to the Google 2FA service.
     *
     */
    public function store(Google2faRequest $request, string $googleCode): RedirectResponse
    {
        return $this->redirectWithServiceResponse($this->google2faService->enable($request, $googleCode));
    }

    /**
     * Purpose: verifies a Google 2FA challenge for the authenticated user.
     *
     * Action: delegates OTP verification and authenticator state updates to the Google 2FA service.
     *
     */
    public function verify(Google2faRequest $request): RedirectResponse
    {
        return $this->redirectWithServiceResponse($this->google2faService->verify($request));
    }


    /**
     * Purpose: disables Google 2FA for the authenticated user.
     *
     * Action: delegates OTP verification and secret removal to the Google 2FA service.
     *
     */
    public function destroy(Google2faRequest $request): RedirectResponse
    {
        return $this->redirectWithServiceResponse($this->google2faService->disable($request));
    }

    /**
     * Purpose: redirects back with the flash message returned by a Google 2FA service operation.
     *
     * Action: keeps controller methods short while preserving the current redirect behavior.
     *
     * @param array<string, mixed> $response
     */
    private function redirectWithServiceResponse(array $response): RedirectResponse
    {
        $flashKey = $response[SERVICE_RESPONSE_STATUS] === true ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($flashKey, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
