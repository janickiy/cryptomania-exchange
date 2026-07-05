<?php

namespace App\Services\User\Admin;

use App\DTO\Admin\StockItemData;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\Core\FileUploadService;

class StockItemService
{
    public function __construct(
        private readonly StockItemInterface $stockItem,
        private readonly WalletInterface $wallet,
        private readonly FileUploadService $fileUploadService,
    ) {
    }

    public function create(StockItemData $data): mixed
    {
        return $this->stockItem->createFromDto($this->withUploadedEmoji($data));
    }

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

    public function delete(int $id): bool
    {
        return $this->stockItem->deleteById($id);
    }

    public function toggleActiveStatus(int $id): array
    {
        $stockItem = $this->stockItem->getFirstById($id, ['stockPairs', 'baseStockPairs']);

        if (empty($stockItem)) {
            return [SERVICE_RESPONSE_STATUS => false, SERVICE_RESPONSE_MESSAGE => __('Stock item could not found.')];
        }

        if ($stockItem->stockPairs->where('is_default', ACTIVE_STATUS_ACTIVE)->first() || $stockItem->baseStockPairs->where('is_default', ACTIVE_STATUS_ACTIVE)->first()) {
            return [SERVICE_RESPONSE_STATUS => false, SERVICE_RESPONSE_MESSAGE => __("The stock item's status can not be deactivated as it's being used in the default stock pair.")];
        }

        if ($updatedStockItem = $this->stockItem->toggleStatusById($id)) {
            return [
                SERVICE_RESPONSE_STATUS => true,
                SERVICE_RESPONSE_MESSAGE => __('The stock item has been :status successfully.', [
                    'status' => $updatedStockItem->is_active == ACTIVE_STATUS_ACTIVE ? 'activated' : 'deactivated',
                ]),
            ];
        }

        return [SERVICE_RESPONSE_STATUS => false, SERVICE_RESPONSE_MESSAGE => __('Failed to change stock item status.')];
    }

    private function withUploadedEmoji(StockItemData $data): StockItemData
    {
        if (!$data->itemEmojiFile) {
            return $data;
        }

        $path = $this->fileUploadService->upload($data->itemEmojiFile, config('commonconfig.path_stock_item_emoji'), 'item_emoji', 'stock', $data->item, 'public', 100, 100);

        return $data->withItemEmoji($path ?: null);
    }
}
