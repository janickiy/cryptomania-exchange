<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminSettingRequest;
use App\Services\Core\AdminSettingService;

class AdminSettingController extends Controller
{
    public $adminSettingService;

    /**
     * @param AdminSettingService $adminSettingService
     */
    public function __construct(AdminSettingService $adminSettingService)
    {
        $this->adminSettingService = $adminSettingService;
    }

    /**
     * @param string $adminSettingType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index($adminSettingType = 'general')
    {
        $data['settings'] = $this->adminSettingService->adminForm($adminSettingType, true);
        $data['adminSettingType'] = $adminSettingType;
        $data['title'] = __('Admin Setting');

        return view('backend.adminSetting.index', $data);
    }

    /**
     * @param $adminSettingType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit($adminSettingType)
    {
        $data['settings'] = $this->adminSettingService->adminForm($adminSettingType);
        $data['adminSettingType'] = $adminSettingType;
        $data['title'] = __('Edit Admin Setting');

        return view('backend.adminSetting.edit', $data);
    }

    /**
     * @param AdminSettingRequest $request
     * @param $adminSettingType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AdminSettingRequest $request, $adminSettingType)
    {
        $response = $this->adminSettingService->adminUpdate($request, $adminSettingType);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->route('admin-settings.edit', $adminSettingType)->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
