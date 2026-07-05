<?php

namespace App\Http\Controllers\User\Trader;

use App\Http\Requests\User\IdRequest;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Services\Core\FileUploadService;
use App\Services\User\ProfileService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class IdController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела верификации личности пользователя.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly ProfileService $profileService,
        private readonly FileUploadService $fileUploadService,
        private readonly UserInfoInterface $userInfo,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела верификации личности пользователя.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
    {
        $data = $this->profileService->profile();
        $data['title'] = __('Upload ID');

        return view('backend.uploadID.index', $data);
    }

    /**
     * Назначение: создает новую запись в разделе верификации личности пользователя.
     *
     * Действие: принимает валидированный запрос, передает данные в сервис или репозиторий и возвращает результат операции.
     */
    public function store(IdRequest $request): RedirectResponse
    {
        $attributes = $request->only('id_type');
        $attributes['is_id_verified'] = ID_STATUS_PENDING;

        $uploadedIdFiles = [];

        foreach ($request->allFiles() as $fieldName => $file) {
            $uploadedIdFiles[$fieldName] = $this->fileUploadService->upload($file, config('commonconfig.path_id_image'), $fieldName, 'id', Auth::id(), 'public');
        }

        if (!empty($uploadedIdFiles)) {
            $attributes = array_merge($attributes, $uploadedIdFiles);

            if ($this->userInfo->updateByConditions($attributes, ['user_id' => Auth::id(), 'is_id_verified' => ID_STATUS_UNVERIFIED])) {
                return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('ID has been uploaded successfully.'));
            }
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to upload ID.'));
    }
}
