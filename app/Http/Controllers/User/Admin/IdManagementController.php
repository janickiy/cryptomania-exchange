<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Services\User\Admin\IdManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class IdManagementController extends Controller
{
    /**
     * Purpose: initializes the IdManagementController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly IdManagementService $idManagementService)
    {
    }

    /**
     * Purpose: displays ID verification requests for administrator review.
     *
     * Action: delegates list preparation to the service layer and returns the ID management index view.
     *
     */
    public function index(): View
    {
        return view('backend.idManagement.index', $this->idManagementService->indexData());
    }

    /**
     * Purpose: displays one ID verification request.
     *
     * Action: delegates request loading to the service layer and returns the detail view.
     *
     */
    public function show(int|string $id): View
    {
        return view('backend.idManagement.show', $this->idManagementService->showData($id));
    }

    /**
     * Purpose: approves a pending ID verification request.
     *
     * Action: delegates the approval workflow to the service layer and redirects back with a flash message.
     *
     */
    public function approve(int|string $id): RedirectResponse
    {
        return $this->redirectBackWithResponse($this->idManagementService->approve($id));
    }

    /**
     * Purpose: declines a pending ID verification request.
     *
     * Action: delegates the decline workflow to the service layer and redirects to the list after success.
     *
     */
    public function decline(int|string $id): RedirectResponse
    {
        $response = $this->idManagementService->decline($id);

        if ($response[SERVICE_RESPONSE_STATUS]) {
            return redirect()
                ->route('admin.id-management.index')
                ->with(SERVICE_RESPONSE_SUCCESS, $response[SERVICE_RESPONSE_MESSAGE]);
        }

        return $this->redirectBackWithResponse($response);
    }

    /**
     * Purpose: redirects back with an ID management operation response.
     *
     * Action: maps the service response status to the expected flash message key.
     *
     * @param array<string, bool|string> $response
     */
    private function redirectBackWithResponse(array $response): RedirectResponse
    {
        $flashKey = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($flashKey, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
