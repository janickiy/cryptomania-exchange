<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\LoginRequest;
use App\Http\Requests\Core\NewPasswordRequest;
use App\Http\Requests\Core\PasswordResetRequest;
use App\Http\Requests\Core\RegisterRequest;
use App\Services\Guest\AuthService;
use App\Services\User\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    /**
     * Purpose: initializes the AuthController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly AuthService $service,
        private readonly UserService $userService,
    ) {
    }

    /**
     * Purpose: handles the login form action in AuthController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     * @return View
     */
    public function loginForm(): View
    {
        return view('backend.login');
    }

    /**
     * Purpose: handles user login.
     *
     * Action: passes credentials to the auth service and returns the authentication result.
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $response = $this->service->login($request);

        if ($response[SERVICE_RESPONSE_STATUS]) {
            return redirect()->route(REDIRECT_ROUTE_TO_USER_AFTER_LOGIN)->with(SERVICE_RESPONSE_SUCCESS, $response[SERVICE_RESPONSE_MESSAGE]);
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: ends the user session.
     *
     * Action: logs the user out and redirects to a safe page.
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        $redirectRoute = app('router')->getRoutes()->match(app('request')->create(session('_previous')['url']))->getName();

        if ($redirectRoute != 'exchange.index') {
            $redirectRoute = 'login';
        }

        Auth::logout();
        session()->flush();

        return redirect()->route($redirectRoute)->with(SERVICE_RESPONSE_SUCCESS, __('You have been logged out successfully.'));
    }

    /**
     * Purpose: creates a user account from registration data.
     *
     * Action: passes validated data to the service and returns the account creation result.
     * @return View
     */
    public function register(): View
    {
        return view('backend.register');
    }

    /**
     * Purpose: handles the store user action in AuthController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     * @param RegisterRequest $request
     * @return RedirectResponse
     */
    public function storeUser(RegisterRequest $request): RedirectResponse
    {
        $parameters = $request->only(['first_name', 'last_name', 'email', 'username', 'password', 'referral_code']);

        if ($this->userService->generate($parameters)) {
            return redirect()->route('login')->with(SERVICE_RESPONSE_SUCCESS, __('Registration successful. Please check your email to verify your account.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Registration failed. Please try after sometime.'));
    }

    /**
     * Purpose: handles the forget password action in AuthController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     * @return View
     */
    public function forgetPassword(): View
    {
        return view('backend.forget_password');
    }

    /**
     * Purpose: handles the send password reset mail action in AuthController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     * @param PasswordResetRequest $request\
     * @return RedirectResponse
     */
    public function sendPasswordResetMail(PasswordResetRequest $request): RedirectResponse
    {
        $response = $this->service->sendPasswordResetMail($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: handles the reset password action in AuthController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     * @param Request $request
     * @param int|string $id
     * @return View
     */
    public function resetPassword(Request $request, int|string $id): View
    {
        $data = $this->service->resetPassword($request, $id);

        return view('backend.reset_password', $data);
    }

    /**
     * Purpose: handles the update password action in AuthController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     * @param NewPasswordRequest $request
     * @param int|string $id
     * @return RedirectResponse
     */
    public function updatePassword(NewPasswordRequest $request, int|string $id): RedirectResponse
    {
        $response = $this->service->updatePassword($request, $id);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->route('login')->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
