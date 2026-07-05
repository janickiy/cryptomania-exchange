<?php

namespace App\Http\Controllers\User\Admin;

use App\DTO\Admin\StockPairData;
use App\Http\Requests\Admin\StockPairRequest;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Services\Core\DataListService;
use App\Http\Controllers\Controller;
use App\Services\User\Admin\StockPairService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class StockPairController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела торговых пар.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly StockPairInterface $stockPair,
        private readonly StockItemInterface $stockItems,
        private readonly StockPairService $stockPairService,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела торговых пар.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
    {
        $searchFields = [
            ['stock_items.item', __('Exchangeable Item')],
            ['stock_items.item_name', __('Exchangeable Item Name')],
            ['base_items.item', __('Base Item')],
            ['base_items.item_name', __('Base Item Name')],
            ['stock_pairs.is_active', __('Active Status')],
        ];
        $orderFields = [
            ['stock_items.item', __('Exchangeable Item')],
            ['base_items.item', __('Base Item')],
            ['stock_items.created_at', __('Created Date')],
        ];
        $joinArray = [
            // connected table, connected field, operator, this table field
            ['stock_items as base_items', 'base_items.id', '=', 'stock_pairs.base_item_id'],
            ['stock_items', 'stock_items.id', '=', 'stock_pairs.stock_item_id'],
        ];
        $select = [
            'stock_pairs.id as id',
            'base_item_id',
            'stock_item_id',
            'base_items.item as base_stock_item',
            'base_items.item_name as base_stock_name',
            'stock_items.item as stock_item',
            'stock_items.item_name as stock_name',
            'last_price',
            'stock_pairs.is_active',
            'is_default',
            'stock_pairs.created_at'
        ];
        $query = $this->stockPair->paginateWithFilters($searchFields, $orderFields, null, $select, $joinArray);
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Stock Pairs');

        return view('backend.stockPairs.index', $data);
    }

    /**
     * Назначение: показывает форму создания записи в разделе торговых пар.
     *
     * Действие: подготавливает справочные данные для формы и возвращает представление создания.
     */
    public function create(): View|Factory|Application
    {
        $data['stockItems'] = $this->stockItems->getActiveList()->pluck('item', 'id')->toArray();
        $data['title'] = __('Create Stock Pair');

        return view('backend.stockPairs.create', $data);
    }

    /**
     * Назначение: создает новую запись в разделе торговых пар.
     *
     * Действие: принимает валидированный запрос, передает данные в сервис или репозиторий и возвращает результат операции.
     */
    public function store(StockPairRequest $request): RedirectResponse
    {
        try {
            $created = $this->stockPairService->create(StockPairData::fromArray($request->validated()));

            return redirect()->route('admin.stock-pairs.show', $created->id)->with(SERVICE_RESPONSE_SUCCESS, __('The stock pair has been created successfully.'));
        } catch (\Exception $exception) {
            if ($exception->getCode() == 23000) {
                return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('The stock pair already exists.'));
            }

            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create stock pair.'));
        }
    }

    /**
     * Назначение: показывает детальную страницу записи в разделе торговых пар.
     *
     * Действие: находит запись по идентификатору, подготавливает связанные данные и возвращает представление просмотра.
     */
    public function show(int|string $id): View|Factory|Application
    {
        $data['title'] = __('Stock Pair');
        $data['stockPair'] = $this->stockPair->getFirstStockPairDetailByConditions(['stock_pairs.id' => $id]);

        return view('backend.stockPairs.show', $data);
    }

    /**
     * Назначение: показывает форму редактирования записи в разделе торговых пар.
     *
     * Действие: загружает запись и справочные данные, затем возвращает представление формы редактирования.
     */
    public function edit(int|string $id): View|Factory|Application
    {
        $data['stockItems'] = $this->stockItems->getActiveList()->pluck('item', 'id')->toArray();
        $data['title'] = __('Edit Stock Pair');
        $data['stockPair'] = $this->stockPair->findOrFailById($id);

        return view('backend.stockPairs.edit', $data);
    }

    /**
     * Назначение: обновляет запись в разделе торговых пар.
     *
     * Действие: принимает валидированный запрос, передает изменения в сервис или репозиторий и возвращает ответ с результатом.
     */
    public function update(StockPairRequest $request, int|string $id): RedirectResponse
    {
        if ($this->stockPairService->update((int) $id, StockPairData::fromArray($request->validated()))) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock pair has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update.'));
    }

    /**
     * Назначение: удаляет запись в разделе торговых пар.
     *
     * Действие: проверяет возможность удаления, выполняет операцию через сервис или репозиторий и возвращает результат.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        try {
            if ($this->stockPairService->delete((int) $id)) {
                return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock pair has been deleted successfully.'));
            }

            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete.'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete as the stock pair is being used.'));
        }
    }

    /**
     * Назначение: переключает активность записи в разделе торговых пар.
     *
     * Действие: меняет статус активности через сервис или репозиторий и возвращает сообщение о результате.
     */
    public function toggleActiveStatus(int|string $id): RedirectResponse
    {
        $response = $this->stockPairService->toggleActiveStatus((int) $id);

        return redirect()->back()->with($response);
    }

    /**
     * Назначение: назначает запись значением по умолчанию в разделе торговых пар.
     *
     * Действие: снимает предыдущий default-статус, устанавливает новый и возвращает результат операции.
     */
    public function makeStatusDefault(int|string $id): RedirectResponse
    {
        try {
            $this->stockPairService->makeDefault((int) $id);
        } catch (\Exception $exception) {
            logs()->error("Make default stock pair: " . $exception->getMessage());
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to make default.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock pair has been made default successfully.'));
    }
}
