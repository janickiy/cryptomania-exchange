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
     * Purpose: initializes the IdController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly ProfileService $profileService,
        private readonly FileUploadService $fileUploadService,
        private readonly UserInfoInterface $userInfo,
    ) {
    }

    /**
     * Purpose: shows the main page or record list for the section.
     *
     * Action: collects data through services or repositories and returns the view.
     *
     */
    public function index(): View|Factory|Application
    {
        $data = $this->profileService->profile();
        $data['title'] = __('Upload ID');

        return view('backend.uploadID.index', $data);
    }

    /**
     * Purpose: creates a new record from request data.
     *
     * Action: passes validated data to the service layer and returns the operation result.
     *
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
