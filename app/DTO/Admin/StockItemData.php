<?php

namespace App\DTO\Admin;

use App\DTO\DataTransferObject;
use Illuminate\Http\UploadedFile;

final readonly class StockItemData implements DataTransferObject
{
    public function __construct(
        public string $item,
        public string $itemName,
        public int $itemType,
        public int $isActive,
        public int $isIco,
        public int $exchangeStatus = ACTIVE_STATUS_INACTIVE,
        public int $depositStatus = ACTIVE_STATUS_INACTIVE,
        public string|int|float $depositFee = 0,
        public int $withdrawalStatus = ACTIVE_STATUS_INACTIVE,
        public string|int|float $withdrawalFee = 0,
        public string|int|float $minimumWithdrawalAmount = 0,
        public string|int|float $dailyWithdrawalLimit = 0,
        public ?int $apiService = null,
        public ?UploadedFile $itemEmojiFile = null,
        public ?string $itemEmoji = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $isIco = (int) ($data['is_ico'] ?? ACTIVE_STATUS_INACTIVE);
        $itemType = (int) $data['item_type'];
        $depositStatus = (int) ($data['deposit_status'] ?? ACTIVE_STATUS_INACTIVE);
        $withdrawalStatus = (int) ($data['withdrawal_status'] ?? ACTIVE_STATUS_INACTIVE);
        $apiService = $data['api_service'] ?? null;

        return new self(
            item: strtoupper(trim((string) $data['item'])),
            itemName: trim((string) $data['item_name']),
            itemType: $itemType,
            isActive: (int) $data['is_active'],
            isIco: $isIco,
            exchangeStatus: $isIco === ACTIVE_STATUS_ACTIVE ? ACTIVE_STATUS_INACTIVE : (int) ($data['exchange_status'] ?? ACTIVE_STATUS_INACTIVE),
            depositStatus: $isIco === ACTIVE_STATUS_ACTIVE ? ACTIVE_STATUS_INACTIVE : $depositStatus,
            depositFee: $isIco === ACTIVE_STATUS_ACTIVE ? 0 : ($data['deposit_fee'] ?? 0),
            withdrawalStatus: $isIco === ACTIVE_STATUS_ACTIVE ? ACTIVE_STATUS_INACTIVE : $withdrawalStatus,
            withdrawalFee: $isIco === ACTIVE_STATUS_ACTIVE ? 0 : ($data['withdrawal_fee'] ?? 0),
            minimumWithdrawalAmount: $isIco === ACTIVE_STATUS_ACTIVE ? 0 : ($data['minimum_withdrawal_amount'] ?? 0),
            dailyWithdrawalLimit: $isIco === ACTIVE_STATUS_ACTIVE ? 0 : ($data['daily_withdrawal_limit'] ?? 0),
            apiService: $isIco === ACTIVE_STATUS_ACTIVE || !in_array($itemType, config('commonconfig.currency_transferable'), true) ? null : ($apiService !== null ? (int) $apiService : null),
            itemEmojiFile: $data['item_emoji'] ?? null,
            itemEmoji: $data['item_emoji_path'] ?? null,
        );
    }

    public function withItemEmoji(?string $itemEmoji): self
    {
        return new self(
            item: $this->item,
            itemName: $this->itemName,
            itemType: $this->itemType,
            isActive: $this->isActive,
            isIco: $this->isIco,
            exchangeStatus: $this->exchangeStatus,
            depositStatus: $this->depositStatus,
            depositFee: $this->depositFee,
            withdrawalStatus: $this->withdrawalStatus,
            withdrawalFee: $this->withdrawalFee,
            minimumWithdrawalAmount: $this->minimumWithdrawalAmount,
            dailyWithdrawalLimit: $this->dailyWithdrawalLimit,
            apiService: $this->apiService,
            itemEmojiFile: $this->itemEmojiFile,
            itemEmoji: $itemEmoji,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'item' => $this->item,
            'item_name' => $this->itemName,
            'item_type' => $this->itemType,
            'is_active' => $this->isActive,
            'is_ico' => $this->isIco,
            'exchange_status' => $this->exchangeStatus,
            'deposit_status' => $this->depositStatus,
            'deposit_fee' => $this->depositFee,
            'withdrawal_status' => $this->withdrawalStatus,
            'withdrawal_fee' => $this->withdrawalFee,
            'minimum_withdrawal_amount' => $this->minimumWithdrawalAmount,
            'daily_withdrawal_limit' => $this->dailyWithdrawalLimit,
            'api_service' => $this->apiService,
        ];

        if ($this->itemEmoji !== null) {
            $data['item_emoji'] = $this->itemEmoji;
        }

        return $data;
    }
}
