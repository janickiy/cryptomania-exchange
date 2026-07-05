<?php

namespace App\Http\Controllers\Core;

use App\Services\Core\NavService;
use App\Http\Requests\Admin\NavRequest;
use App\Http\Controllers\Controller;

class NavController extends Controller
{
    /**
     * @param null $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index($slug = null)
    {
        $data = app(NavService::class)->backendMenuBuilder($slug);
        $data['title'] = __('Navigation');

        return view('backend.navigation.index', $data);
    }

    /**
     * @param NavRequest $request
     * @param $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(NavRequest $request, $slug)
    {
        $response = app(NavService::class)->backendMenuSave($request, $slug);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
