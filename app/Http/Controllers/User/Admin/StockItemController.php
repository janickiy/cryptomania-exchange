<?php

namespace App\Http\Controllers\User\Admin;

use App\DTO\Admin\StockItemData;
use App\Http\Requests\Admin\StockItemRequest;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Http\Controllers\Controller;
use App\Services\Core\DataListService;
use App\Services\User\Admin\StockItemService;

class StockItemController extends Controller
{
    public $stockItem;

    /**
     * StockItemController constructor.
     * @param StockItemInterface $stockItem
     */
    public function __construct(StockItemInterface $stockItem)
    {
        $this->stockItem = $stockItem;
    }

    /**
     * @description:
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $searchFields = [
            ['item', __('Stock Item')],
            ['item_name', __('Stock Item Name')],
            ['item_type', __('Stock Item Type')],
            ['is_active', __('Active Status')],
        ];

        $orderFields = [
            ['item', __('Stock Item')],
            ['item_name', __('Stock Item Name')],
            ['item_type', __('Stock Item Type')],
            ['stock_items.created_at', __('Created Date')],
        ];

        $query = $this->stockItem->paginateWithFilters($searchFields, $orderFields);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Stock Items');

        return view('backend.stockItems.index', $data);
    }

    /**
     * @description:
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data['title'] = __('Create Stock Item');

        return view('backend.stockItems.create', $data);
    }

    /**
     * @description:
     * @param StockItemRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StockItemRequest $request)
    {
        if ($created = app(StockItemService::class)->create(StockItemData::fromArray($request->validated() + ['item_emoji' => $request->file('item_emoji')]))) {
            return redirect()->route('admin.stock-items.show', $created->id)->with(SERVICE_RESPONSE_SUCCESS, __('The stock item has been created successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create stock item.'));
    }

    /**
     * @description:
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $data['title'] = __('Stock Item');
        $data['stockItem'] = $this->stockItem->findOrFailById($id);

        return view('backend.stockItems.show', $data);
    }

    /**
     * @description:
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data['title'] = __('Edit Stock Item');
        $data['stockItem'] = $this->stockItem->findOrFailById($id);

        return view('backend.stockItems.edit', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-16 6:46 PM
     * @description:
     * @param StockItemRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StockItemRequest $request, $id)
    {
        if (app(StockItemService::class)->update((int) $id, StockItemData::fromArray($request->validated() + ['item_emoji' => $request->file('item_emoji')]))) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock item has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update.'));
    }

    /**
     * @description:
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            if (app(StockItemService::class)->delete((int) $id)) {
                return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock item has been deleted successfully.'));
            }

            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete.'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete as the stock item is being used.'));
        }
    }

    /**
     * @description:
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActiveStatus($id)
    {
        $response = app(StockItemService::class)->toggleActiveStatus((int) $id);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
