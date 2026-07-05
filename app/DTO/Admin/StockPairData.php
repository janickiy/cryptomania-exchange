<?php

namespace App\DTO\Admin;

use App\DTO\DataTransferObject;

final readonly class StockPairData implements DataTransferObject
{
    public function __construct(
        public int $stockItemId,
        public int $baseItemId,
        public string|float|int $lastPrice,
        public ?int $isActive = null,
        public ?int $isDefault = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            stockItemId: (int) $data['stock_item_id'],
            baseItemId: (int) $data['base_item_id'],
            lastPrice: $data['last_price'],
            isActive: isset($data['is_active']) ? (int) $data['is_active'] : null,
            isDefault: isset($data['is_default']) ? (int) $data['is_default'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'stock_item_id' => $this->stockItemId,
            'base_item_id' => $this->baseItemId,
            'last_price' => $this->lastPrice,
            'is_active' => $this->isActive,
            'is_default' => $this->isDefault,
        ], static fn ($value) => $value !== null);
    }
}
