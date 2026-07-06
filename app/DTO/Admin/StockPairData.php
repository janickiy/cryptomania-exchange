<?php

namespace App\DTO\Admin;

use App\DTO\DataTransferObject;

final readonly class StockPairData implements DataTransferObject
{
    /**
     * Purpose: initializes the StockPairData instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        public int $stockItemId,
        public int $baseItemId,
        public string|float|int $lastPrice,
        public ?int $isActive = null,
        public ?int $isDefault = null,
    ) {
    }

    /**
     * Purpose: creates a DTO from an input array.
     *
     * Action: passes validated data between layers without unstructured arrays.
     *
.
     *
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
     * Purpose: converts the DTO back to an array.
     *
     * Action: provides data to repositories, models, or APIs in the expected format.
     *
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
