<?php

namespace App\Services\User\Admin;

use App\DTO\Admin\StockItemData;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\Core\DataListService;
use App\Services\Core\FileUploadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class StockItemService
{
    /**
     * Purpose: initializes the StockItemService instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly StockItemInterface $stockItem,
        private readonly WalletInterface $wallet,
        private readonly FileUploadService $fileUploadService,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: prepares the stock item list page data.
     *
     * Action: builds the filtered stock item list and metadata required by the index view.
     */
    public function indexData(): array
    {
        $searchFields = $this->searchFields();
        $orderFields = $this->orderFields();
        $query = $this->stockItem->paginateWithFilters($searchFields, $orderFields);

        return [
            'list' => $this->dataListService->dataList($query, $searchFields, $orderFields),
            'title' => __('Stock Items'),
        ];
    }

    /**
     * Purpose: prepares the stock item create page data.
     *
     * Action: returns metadata required by the create view.
     */
    public function createData(): array
    {
        return [
            'title' => __('Create Stock Item'),
        ];
    }

    /**
     * Purpose: prepares the stock item detail page data.
     *
     * Action: loads the selected stock item and returns metadata required by the show view.
     */
    public function showData(int|string $id): array
    {
        return [
            'title' => __('Stock Item'),
            'stockItem' => $this->stockItem->findOrFailById($id),
        ];
    }

    /**
     * Purpose: prepares the stock item edit page data.
     *
     * Action: loads the selected stock item and returns metadata required by the edit view.
     */
    public function editData(int|string $id): array
    {
        return [
            'title' => __('Edit Stock Item'),
            'stockItem' => $this->stockItem->findOrFailById($id),
        ];
    }

    /**
     * Purpose: creates a stock item from validated request data.
     *
     * Action: converts validated input into a DTO before running the create workflow.
     */
    public function createFromValidatedData(array $validated, UploadedFile|array|null $itemEmojiFile): Model|false
    {
        return $this->create($this->dataFromArray($validated, $this->uploadedFile($itemEmojiFile)));
    }

    /**
     * Purpose: updates a stock item from validated request data.
     *
     * Action: converts validated input into a DTO before running the update workflow.
     */
    public function updateFromValidatedData(int|string $id, array $validated, UploadedFile|array|null $itemEmojiFile): bool
    {
        return $this->update((int) $id, $this->dataFromArray($validated, $this->uploadedFile($itemEmojiFile)));
    }

    /**
     * Purpose: executes the create service operation.
     *
     * Action: persists a new stock item and uploads its emoji when provided.
     *
     */
    public function create(StockItemData $data): Model|false
    {
        return $this->stockItem->createFromDto($this->withUploadedEmoji($data));
    }

    /**
     * Purpose: executes the update service operation.
     *
     * Action: updates stock item data and resets wallet addresses when the API service changes.
     *
     */
    public function update(int $id, StockItemData $data): bool
    {
        $stockItem = $this->stockItem->getFirstById($id);

        if (empty($stockItem)) {
            return false;
        }

        $updated = $this->stockItem->updateFromDto($id, $this->withUploadedEmoji($data));

        if ($updated && $data->itemType === CURRENCY_CRYPTO && $data->apiService !== (int) $stockItem->api_service) {
            $this->wallet->updateAllByConditions(['address' => null], ['stock_item_id' => $id, ['address', '!=', null]]);
        }

        return $updated;
    }

    /**
     * Purpose: executes the delete service operation.
     *
     * Action: deletes a stock item by id and returns the repository result.
     *
     */
    public function delete(int|string $id): bool
    {
        return $this->stockItem->deleteById((int) $id);
    }

    /**
     * Purpose: deletes a stock item and formats the user-facing response.
     *
     * Action: captures usage-related delete failures so controllers only redirect with the result.
     */
    public function deleteWithResponse(int|string $id): array
    {
        try {
            if ($this->delete($id)) {
                return $this->response(true, __('The stock item has been deleted successfully.'));
            }

            return $this->response(false, __('Failed to delete.'));
        } catch (\Throwable) {
            return $this->response(false, __('Failed to delete as the stock item is being used.'));
        }
    }

    /**
     * Purpose: executes the toggle active status service operation.
     *
     * Action: changes the active status when the stock item is not used by a default stock pair.
     *
     */
    public function toggleActiveStatus(int|string $id): array
    {
        $stockItem = $this->stockItem->getFirstById((int) $id, ['stockPairs', 'baseStockPairs']);

        if (empty($stockItem)) {
            return $this->response(false, __('Stock item could not found.'));
        }

        if ($stockItem->stockPairs->where('is_default', ACTIVE_STATUS_ACTIVE)->first() || $stockItem->baseStockPairs->where('is_default', ACTIVE_STATUS_ACTIVE)->first()) {
            return $this->response(false, __("The stock item's status can not be deactivated as it's being used in the default stock pair."));
        }

        if ($updatedStockItem = $this->stockItem->toggleStatusById((int) $id)) {
            return $this->response(
                true,
                __('The stock item has been :status successfully.', [
                    'status' => $updatedStockItem->is_active == ACTIVE_STATUS_ACTIVE ? 'activated' : 'deactivated',
                ])
            );
        }

        return $this->response(false, __('Failed to change stock item status.'));
    }

    /**
     * Purpose: executes the with uploaded emoji service operation.
     *
     * Action: uploads a new emoji file and returns an immutable DTO with the uploaded path.
     *
     */
    private function withUploadedEmoji(StockItemData $data): StockItemData
    {
        if (!$data->itemEmojiFile) {
            return $data;
        }

        $path = $this->fileUploadService->upload($data->itemEmojiFile, config('commonconfig.path_stock_item_emoji'), 'item_emoji', 'stock', $data->item, 'public', 100, 100);

        return $data->withItemEmoji($path ?: null);
    }

    /**
     * Purpose: converts validated request input into a stock item DTO.
     *
     * Action: attaches the uploaded emoji file to the validated payload before DTO normalization.
     */
    private function dataFromArray(array $validated, ?UploadedFile $itemEmojiFile): StockItemData
    {
        return StockItemData::fromArray($validated + ['item_emoji' => $itemEmojiFile]);
    }

    /**
     * Purpose: normalizes a request file value to a single uploaded file.
     *
     * Action: protects DTO creation from unexpected multi-file payloads.
     */
    private function uploadedFile(UploadedFile|array|null $itemEmojiFile): ?UploadedFile
    {
        return $itemEmojiFile instanceof UploadedFile ? $itemEmojiFile : null;
    }

    /**
     * Purpose: defines searchable stock item list fields.
     *
     * Action: keeps list filter configuration out of the controller.
     */
    private function searchFields(): array
    {
        return [
            ['item', __('Stock Item')],
            ['item_name', __('Stock Item Name')],
            ['item_type', __('Stock Item Type')],
            ['is_active', __('Active Status')],
        ];
    }

    /**
     * Purpose: defines sortable stock item list fields.
     *
     * Action: keeps list ordering configuration out of the controller.
     */
    private function orderFields(): array
    {
        return [
            ['item', __('Stock Item')],
            ['item_name', __('Stock Item Name')],
            ['item_type', __('Stock Item Type')],
            ['stock_items.created_at', __('Created Date')],
        ];
    }

    /**
     * Purpose: builds a standard service response.
     *
     * Action: provides controllers with a consistent status and message structure.
     */
    private function response(bool $status, string $message): array
    {
        return [
            SERVICE_RESPONSE_STATUS => $status,
            SERVICE_RESPONSE_MESSAGE => $message,
        ];
    }
}
