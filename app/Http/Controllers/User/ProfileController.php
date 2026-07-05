<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\PasswordUpdateRequest;
use App\Http\Requests\User\UserAvatarRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Requests\User\UserSettingRequest;
use App\Services\User\ProfileService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела профиля пользователя.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(private readonly ProfileService $service)
    {
    }

    /**
     * Назначение: показывает основную страницу или список раздела профиля пользователя.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
    {
        $data = $this->service->profile();
        $data['title'] = __('Profile');

        return view('backend.profile.index', $data);
    }

    /**
     * Назначение: показывает форму редактирования записи в разделе профиля пользователя.
     *
     * Действие: загружает запись и справочные данные, затем возвращает представление формы редактирования.
     */
    public function edit(): View|Factory|Application
    {
        $data = $this->service->profile();
        $data['title'] = __('Edit Profile');

        return view('backend.profile.edit', $data);
    }

    /**
     * Назначение: обновляет запись в разделе профиля пользователя.
     *
     * Действие: принимает валидированный запрос, передает изменения в сервис или репозиторий и возвращает ответ с результатом.
     */
    public function update(UserRequest $request): RedirectResponse
    {
        $response = $this->service->updatePersonalInfo($request->only(['first_name', 'last_name', 'address']));
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->route('profile.edit')->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Назначение: показывает форму смены пароля.
     *
     * Действие: загружает данные профиля и возвращает представление формы смены пароля.
     */
    public function changePassword(): View|Factory|Application
    {
        $data = $this->service->profile();
        $data['title'] = __('Change Password');

        return view('backend.profile.change_password', $data);
    }

    /**
     * Назначение: обновляет пароль пользователя.
     *
     * Действие: принимает валидированный пароль, передает его в сервис и возвращает результат операции.
     */
    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $response = $this->service->updatePassword($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Назначение: показывает настройки профиля пользователя.
     *
     * Действие: получает данные профиля и возвращает страницу текущих настроек.
     */
    public function setting(): View|Factory|Application
    {
        $data = $this->service->profile();
        $data['title'] = __('Setting');

        return view('backend.profile.setting', $data);
    }

    /**
     * Назначение: показывает форму редактирования настроек профиля.
     *
     * Действие: получает данные профиля и возвращает форму изменения языка и часового пояса.
     */
    public function settingEdit(): View|Factory|Application
    {
        $data = $this->service->profile();
        $data['title'] = __('Edit Setting');

        return view('backend.profile.setting_edit_form', $data);
    }

    /**
     * Назначение: обновляет настройки профиля пользователя.
     *
     * Действие: принимает валидированные настройки, передает их в сервис профиля и возвращает результат.
     */
    public function settingUpdate(UserSettingRequest $request): RedirectResponse
    {
        $response = $this->service->updateSettings([
            'language' => $request->get('language', config('app.locale')),
            'timezone' => $request->get('timezone', config('app.timezone')),
        ]);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->route('profile.setting.edit')->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Назначение: показывает форму изменения аватара.
     *
     * Действие: получает данные профиля и возвращает форму загрузки нового изображения.
     */
    public function avatarEdit(): View|Factory|Application
    {
        $data = $this->service->profile();
        $data['title'] = __('Change Avatar');

        return view('backend.profile.avatar_edit_form', $data);
    }

    /**
     * Назначение: обновляет аватар пользователя.
     *
     * Действие: принимает валидированный файл изображения, передает его в сервис профиля и возвращает результат загрузки.
     */
    public function avatarUpdate(UserAvatarRequest $request): RedirectResponse
    {
        $response = $this->service->avatarUpload($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Назначение: показывает реферальную страницу пользователя.
     *
     * Действие: возвращает представление с текущим пользователем и его реферальными данными.
     */
    public function referral(): View|Factory|Application
    {
        return view('backend.profile.referral', [
            'title' => __('Referral'),
            'user' => Auth::user(),
        ]);
    }

    /**
     * Назначение: создает или обновляет реферальную ссылку пользователя.
     *
     * Действие: вызывает сервис профиля и возвращает flash-сообщение с результатом генерации.
     */
    public function generateReferralLink(): RedirectResponse
    {
        $response = $this->service->generateReferralLink();

        if (!empty($response[SERVICE_RESPONSE_MESSAGE])) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, $response[SERVICE_RESPONSE_MESSAGE]);
        }

        return redirect()->back();
    }
}
