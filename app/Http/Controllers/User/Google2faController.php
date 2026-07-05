<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\Google2faRequest;
use App\Repositories\User\Interfaces\UserInterface;
use App\Services\User\ProfileService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2faController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела двухфакторной аутентификации.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly ProfileService $profileService,
        private readonly UserInterface $users,
    ) {
    }

    /**
     * Назначение: показывает форму создания записи в разделе двухфакторной аутентификации.
     *
     * Действие: подготавливает справочные данные для формы и возвращает представление создания.
     */
    public function create(): View|Factory|Application
    {
        $data = $this->profileService->profile();
        $data['title'] = __('Google Two Factor Authentication');

        if (empty(Auth::user()->google2fa_secret)) {
            $google2fa = new Google2FA();
            $data['secretKey'] = $google2fa->generateSecretKey(16);
            $data['inlineUrl'] = $google2fa->getQRCodeInline(company_name(), Auth::user()->email, $data['secretKey']);
        }

        return view('backend.google2fa.create', $data);
    }


    /**
     * Назначение: создает новую запись в разделе двухфакторной аутентификации.
     *
     * Действие: принимает валидированный запрос, передает данные в сервис или репозиторий и возвращает результат операции.
     */
    public function store(Google2faRequest $request, string $googleCode): RedirectResponse
    {
        $google2fa = new Google2FA();

        try {
            if ($google2fa->verifyKey($googleCode, $request->google_app_code)) {
                if ($this->users->update(['google2fa_secret' => $googleCode], Auth::id())) {

                    $authenticator = app(Authenticator::class)->boot($request);
                    $authenticator->login();

                    return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('Google Authentication has been enabled successfully.'));
                }
            }

            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to enable google authentication.'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to enable google authentication.'));
        }

    }

    /**
     * Назначение: подтверждает действие или код пользователя.
     *
     * Действие: передает данные запроса в сервис проверки и возвращает перенаправление по результату.
     */
    public function verify(Google2faRequest $request): RedirectResponse
    {
        $google2fa = new Google2FA();

        try {
            if ($google2fa->verifyKey(Auth::user()->google2fa_secret, $request->google_app_code)) {
                $authenticator = app(Authenticator::class)->boot($request);
                $authenticator->login();

                return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __("The One Time Password was correct."));
            }

            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to verify google authentication.'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to verify google authentication.'));
        }
    }


    /**
     * Назначение: удаляет запись в разделе двухфакторной аутентификации.
     *
     * Действие: проверяет возможность удаления, выполняет операцию через сервис или репозиторий и возвращает результат.
     */
    public function destroy(Google2faRequest $request): RedirectResponse
    {
        $google2fa = new Google2FA();

        try {
            if ($google2fa->verifyKey(Auth::user()->google2fa_secret, $request->google_app_code)) {
                if ($this->users->update(['google2fa_secret' => null], Auth::id())) {
                    return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('Google Authentication has been disabled successfully.'));
                }
            }

            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to disabled google authentication.'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to disabled google authentication.'));
        }
    }
}
