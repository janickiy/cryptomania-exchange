<?php

namespace App\Http\Controllers\User\Admin;

use App\DTO\Admin\StockItemData;
use App\Http\Requests\Admin\StockItemRequest;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Http\Controllers\Controller;
use App\Services\Core\DataListService;
use App\Services\User\Admin\StockItemService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class StockItemController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела валют и торговых активов.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly StockItemInterface $stockItem,
        private readonly StockItemService $stockItemService,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела валют и торговых активов.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
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
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Stock Items');

        return view('backend.stockItems.index', $data);
    }

    /**
     * Назначение: показывает форму создания записи в разделе валют и торговых активов.
     *
     * Действие: подготавливает справочные данные для формы и возвращает представление создания.
     */
    public function create(): View|Factory|Application
    {
        $data['title'] = __('Create Stock Item');

        return view('backend.stockItems.create', $data);
    }

    /**
     * Назначение: создает новую запись в разделе валют и торговых активов.
     *
     * Действие: принимает валидированный запрос, передает данные в сервис или репозиторий и возвращает результат операции.
     */
    public function store(StockItemRequest $request): RedirectResponse
    {
        if ($created = $this->stockItemService->create(StockItemData::fromArray($request->validated() + ['item_emoji' => $request->file('item_emoji')]))) {
            return redirect()->route('admin.stock-items.show', $created->id)->with(SERVICE_RESPONSE_SUCCESS, __('The stock item has been created successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create stock item.'));
    }

    /**
     * Назначение: показывает детальную страницу записи в разделе валют и торговых активов.
     *
     * Действие: находит запись по идентификатору, подготавливает связанные данные и возвращает представление просмотра.
     */
    public function show(int|string $id): View|Factory|Application
    {
        $data['title'] = __('Stock Item');
        $data['stockItem'] = $this->stockItem->findOrFailById($id);

        return view('backend.stockItems.show', $data);
    }

    /**
     * Назначение: показывает форму редактирования записи в разделе валют и торговых активов.
     *
     * Действие: загружает запись и справочные данные, затем возвращает представление формы редактирования.
     */
    public function edit(int|string $id): View|Factory|Application
    {
        $data['title'] = __('Edit Stock Item');
        $data['stockItem'] = $this->stockItem->findOrFailById($id);

        return view('backend.stockItems.edit', $data);
    }

    /**
     * Назначение: обновляет запись в разделе валют и торговых активов.
     *
     * Действие: принимает валидированный запрос, передает изменения в сервис или репозиторий и возвращает ответ с результатом.
     */
    public function update(StockItemRequest $request, int|string $id): RedirectResponse
    {
        if ($this->stockItemService->update((int) $id, StockItemData::fromArray($request->validated() + ['item_emoji' => $request->file('item_emoji')]))) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock item has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update.'));
    }

    /**
     * Назначение: удаляет запись в разделе валют и торговых активов.
     *
     * Действие: проверяет возможность удаления, выполняет операцию через сервис или репозиторий и возвращает результат.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        try {
            if ($this->stockItemService->delete((int) $id)) {
                return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock item has been deleted successfully.'));
            }

            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete.'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete as the stock item is being used.'));
        }
    }

    /**
     * Назначение: переключает активность записи в разделе валют и торговых активов.
     *
     * Действие: меняет статус активности через сервис или репозиторий и возвращает сообщение о результате.
     */
    public function toggleActiveStatus(int|string $id): RedirectResponse
    {
        $response = $this->stockItemService->toggleActiveStatus((int) $id);
        $status = $response[SERVICE_RESPONSE_STATUS] ? SERVICE_RESPONSE_SUCCESS : SERVICE_RESPONSE_ERROR;

        return redirect()->back()->with($status, $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
