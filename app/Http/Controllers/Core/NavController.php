<?php

namespace App\Http\Controllers\Core;

use App\Services\Core\NavService;
use App\Http\Requests\Admin\NavRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class NavController extends Controller
{
    /**
     * Purpose: initializes the NavController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly NavService $navService)
    {
    }

    /**
     * Purpose: shows the main page or record list for the section.
     *
     * Action: collects data through services or repositories and returns the view.
     *
     */
    public function index(?string $slug = null): View
    {
        $data = $this->navService->backendMenuBuilder($slug);
        $data['title'] = __('Navigation');

        return view('backend.navigation.index', $data);
    }

    /**
     * Purpose: handles the save action in NavController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     * @param NavRequest $request
     * @param string $slug
     * @return RedirectResponse
     */
    public function save(NavRequest $request, string $slug): RedirectResponse
    {
        $response = $this->navService->backendMenuSave($request, $slug);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
