<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\PasswordResetRequest;
use App\Services\Core\VerificationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела email-верификации.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(private readonly VerificationService $verificationService)
    {
    }

    /**
     * Назначение: подтверждает действие или код пользователя.
     *
     * Действие: передает данные запроса в сервис проверки и возвращает перенаправление по результату.
     */
    public function verify(Request $request): RedirectResponse
    {
        $response = $this->verificationService->verifyUserEmail($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;
        $route = Auth::check() ? REDIRECT_ROUTE_TO_USER_AFTER_LOGIN : REDIRECT_ROUTE_TO_LOGIN;

        return redirect()->route($route)->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Назначение: показывает форму повторной отправки письма подтверждения.
     *
     * Действие: возвращает представление для запроса нового verification-письма.
     */
    public function resendForm(): View|Factory|Application
    {
        return view('backend.email_verify');
    }

    /**
     * Назначение: отправляет письмо подтверждения повторно.
     *
     * Действие: валидирует запрос, запускает сервис отправки и возвращает результат пользователю.
     */
    public function send(PasswordResetRequest $request): RedirectResponse
    {
        $response = $this->verificationService->sendVerificationLink($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
