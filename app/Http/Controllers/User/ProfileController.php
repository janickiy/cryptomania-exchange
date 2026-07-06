<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\PasswordUpdateRequest;
use App\Http\Requests\User\UserAvatarRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Requests\User\UserSettingRequest;
use App\Services\User\ProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Purpose: initializes the ProfileController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly ProfileService $service)
    {
    }

    /**
     * Purpose: shows the main page or record list for the section.
     *
     * Action: collects data through services or repositories and returns the view.
     *
     */
    public function index(): View
    {
        $data = $this->service->profile();
        $data['title'] = __('Profile');

        return view('backend.profile.index', $data);
    }

    /**
     * Purpose: shows the edit form for the selected record.
     *
     * Action: loads current data and returns the edit view.
     *
     */
    public function edit(): View
    {
        $data = $this->service->profile();
        $data['title'] = __('Edit Profile');

        return view('backend.profile.edit', $data);
    }

    /**
     * Purpose: updates the selected record from request data.
     *
     * Action: passes changes to the service layer and returns a result message.
     *
     */
    public function update(UserRequest $request): RedirectResponse
    {
        $response = $this->service->updatePersonalInfo($request->only(['first_name', 'last_name', 'address']));
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->route('profile.edit')->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: handles the change password action in ProfileController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function changePassword(): View
    {
        $data = $this->service->profile();
        $data['title'] = __('Change Password');

        return view('backend.profile.change_password', $data);
    }

    /**
     * Purpose: handles the update password action in ProfileController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $response = $this->service->updatePassword($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: handles the setting action in ProfileController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function setting(): View
    {
        $data = $this->service->profile();
        $data['title'] = __('Setting');

        return view('backend.profile.setting', $data);
    }

    /**
     * Purpose: handles the setting edit action in ProfileController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function settingEdit(): View
    {
        $data = $this->service->profile();
        $data['title'] = __('Edit Setting');

        return view('backend.profile.setting_edit_form', $data);
    }

    /**
     * Purpose: handles the setting update action in ProfileController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function settingUpdate(UserSettingRequest $request): RedirectResponse
    {
        $response = $this->service->updateSettings([
            'language' => $request->input('language', config('app.locale')),
            'timezone' => $request->input('timezone', config('app.timezone')),
        ]);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->route('profile.setting.edit')->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: handles the avatar edit action in ProfileController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function avatarEdit(): View
    {
        $data = $this->service->profile();
        $data['title'] = __('Change Avatar');

        return view('backend.profile.avatar_edit_form', $data);
    }

    /**
     * Purpose: handles the avatar update action in ProfileController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function avatarUpdate(UserAvatarRequest $request): RedirectResponse
    {
        $response = $this->service->avatarUpload($request);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: handles the referral action in ProfileController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function referral(): View
    {
        return view('backend.profile.referral', [
            'title' => __('Referral'),
            'user' => Auth::user(),
        ]);
    }

    /**
     * Purpose: handles the generate referral link action in ProfileController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
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
