<?php

namespace App\Http\Controllers\Exchange;

use App\DTO\Exchange\IcoPurchaseData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Trader\IcoStoreRequest;
use App\Services\Exchange\IcoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class IcoController extends Controller
{
    /**
     * Purpose: initializes the IcoController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly IcoService $icoService)
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
        return view('frontend.ico.index', $this->icoService->indexData());
    }

    /**
     * Purpose: handles the buy action in IcoController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function buy(int|string $id): View
    {
        return view('frontend.ico.buy', $this->icoService->buyData($id));
    }

    /**
     * Purpose: creates a new record from request data.
     *
     * Action: passes validated data to the service layer and returns the operation result.
     *
     */
    public function store(IcoStoreRequest $request): RedirectResponse
    {
        $response = $this->icoService->purchase(IcoPurchaseData::fromArray($request->validated()));
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
