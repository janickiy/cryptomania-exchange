<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockItemRequest;
use App\Services\User\Admin\StockItemService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StockItemController extends Controller
{
    /**
     * Purpose: initializes the StockItemController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     */
    public function __construct(
        private readonly StockItemService $stockItemService,
    ) {
    }

    /**
     * Purpose: displays the stock item list page.
     *
     * Action: delegates list filtering and metadata preparation to the stock item service.
     */
    public function index(): View
    {
        return view('backend.stockItems.index', $this->stockItemService->indexData());
    }

    /**
     * Purpose: displays the stock item creation form.
     *
     * Action: delegates page metadata preparation to the stock item service.
     */
    public function create(): View
    {
        return view('backend.stockItems.create', $this->stockItemService->createData());
    }

    /**
     * Purpose: creates a stock item from validated request data.
     *
     * Action: delegates DTO creation, upload handling, and persistence to the stock item service.
     */
    public function store(StockItemRequest $request): RedirectResponse
    {
        if ($created = $this->stockItemService->createFromValidatedData($request->validated(), $request->file('item_emoji'))) {
            return redirect()->route('admin.stock-items.show', $created->id)->with(SERVICE_RESPONSE_SUCCESS, __('The stock item has been created successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create stock item.'));
    }

    /**
     * Purpose: displays details for a selected stock item.
     *
     * Action: delegates stock item lookup and page metadata preparation to the service.
     */
    public function show(int|string $id): View
    {
        return view('backend.stockItems.show', $this->stockItemService->showData($id));
    }

    /**
     * Purpose: displays the edit form for a selected stock item.
     *
     * Action: delegates stock item lookup and page metadata preparation to the service.
     */
    public function edit(int|string $id): View
    {
        return view('backend.stockItems.edit', $this->stockItemService->editData($id));
    }

    /**
     * Purpose: updates a stock item from validated request data.
     *
     * Action: delegates DTO creation, upload handling, and persistence to the stock item service.
     */
    public function update(StockItemRequest $request, int|string $id): RedirectResponse
    {
        if ($this->stockItemService->updateFromValidatedData($id, $request->validated(), $request->file('item_emoji'))) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock item has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update.'));
    }

    /**
     * Purpose: deletes a selected stock item.
     *
     * Action: delegates deletion and failure-message selection to the stock item service.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        $response = $this->stockItemService->deleteWithResponse($id);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: toggles the active status of a selected stock item.
     *
     * Action: delegates default-pair checks and status changes to the stock item service.
     */
    public function toggleActiveStatus(int|string $id): RedirectResponse
    {
        $response = $this->stockItemService->toggleActiveStatus($id);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
