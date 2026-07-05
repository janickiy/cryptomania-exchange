<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\LoginRequest;
use App\Http\Requests\Core\NewPasswordRequest;
use App\Http\Requests\Core\PasswordResetRequest;
use App\Http\Requests\Core\RegisterRequest;
use App\Services\Guest\AuthService;
use App\Services\User\UserService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела аутентификации и регистрации.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly AuthService $service,
        private readonly UserService $userService,
    ) {
    }

    /**
     * Назначение: показывает форму входа пользователя.
     *
     * Действие: возвращает представление страницы авторизации.
     */
    public function loginForm(): View|Factory|Application
    {
        return view('backend.login');
    }

    /*
     * login user
     */

    /**
     * Назначение: авторизует пользователя.
     *
     * Действие: проверяет учетные данные через сервис аутентификации и перенаправляет пользователя по результату входа.
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
     * Назначение: завершает пользовательскую сессию.
     *
     * Действие: выходит из аккаунта, очищает сессию и перенаправляет пользователя на допустимый маршрут.
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
     * Назначение: показывает форму регистрации пользователя.
     *
     * Действие: возвращает представление страницы регистрации.
     */
    public function register(): View|Factory|Application
    {
        return view('backend.register');
    }

    /**
     * Назначение: регистрирует нового пользователя.
     *
     * Действие: принимает валидированные регистрационные данные, создает аккаунт и возвращает сообщение о результате.
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
     * Назначение: показывает форму восстановления пароля.
     *
     * Действие: возвращает представление для ввода email и запуска сброса пароля.
     */
    public function forgetPassword(): View|Factory|Application
    {
        return view('backend.forget_password');
    }

    /**
     * Назначение: отправляет письмо для восстановления пароля.
     *
     * Действие: передает валидированный email в сервис сброса пароля и возвращает flash-сообщение.
     */
    public function sendPasswordResetMail(PasswordResetRequest $request): RedirectResponse
    {
        $response = $this->service->sendPasswordResetMail($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Назначение: показывает форму установки нового пароля.
     *
     * Действие: проверяет токен сброса, получает данные из сервиса и возвращает форму нового пароля.
     */
    public function resetPassword(Request $request, int|string $id): View|Factory|Application
    {
        $data = $this->service->resetPassword($request, $id);

        return view('backend.reset_password', $data);
    }

    /**
     * Назначение: обновляет пароль пользователя.
     *
     * Действие: принимает валидированный пароль, передает его в сервис и возвращает результат операции.
     */
    public function updatePassword(NewPasswordRequest $request, int|string $id): RedirectResponse
    {
        $response = $this->service->updatePassword($request, $id);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->route('login')->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
