<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminSettingRequest;
use App\Services\Core\AdminSettingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AdminSettingController extends Controller
{
    /**
     * Purpose: initializes the admin settings controller.
     *
     * Action: receives the service that prepares and updates setting fields.
     *
     */
    public function __construct(private readonly AdminSettingService $adminSettingService)
    {
    }

    /**
     * Purpose: shows an admin setting section in read-only mode.
     *
     * Action: renders the selected settings section with values prepared by the service
     *
     * @param string $adminSettingType
     * @return View
     */
    public function index(string $adminSettingType = 'general'): View
    {
        return $this->settingsView('backend.adminSetting.index', $adminSettingType, __('Admin Setting'), true);
    }

    /**
     * Purpose: shows the edit form for an admin setting section
     *
     * Action: renders editable inputs for the selected settings section.
     *
     * @param string $adminSettingType
     * @return View
     */
    public function edit(string $adminSettingType): View
    {
        return $this->settingsView('backend.adminSetting.edit', $adminSettingType, __('Edit Admin Setting'));
    }

    /**
     * Purpose: updates the selected admin setting section.
     *
     * Action: delegates persistence to the service and redirects back with a flash message.
     *
     * @param AdminSettingRequest $request
     * @param string $adminSettingType
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(AdminSettingRequest $request, string $adminSettingType): RedirectResponse
    {
        $response = $this->adminSettingService->adminUpdate($request, $adminSettingType);

        return redirect()
            ->route('admin-settings.edit', $adminSettingType)
            ->with(
                $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR,
                $response[SERVICE_RESPONSE_MESSAGE]
            );
    }

    /**
     * Purpose: builds an admin setting page response
     *
     * Action: keeps shared view data for read and edit pages in one place.
     *
     * @param string $view
     * @param string $adminSettingType
     * @param string $title
     * @param bool $viewOnly
     * @return View
     */
    private function settingsView(string $view, string $adminSettingType, string $title, bool $viewOnly = false): View
    {
        return view($view, [
            'settings' => $this->adminSettingService->adminForm($adminSettingType, $viewOnly),
            'adminSettingType' => $adminSettingType,
            'title' => $title,
        ]);
    }
}
