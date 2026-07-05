<?php

namespace App\DTO\Exchange;

use App\DTO\DataTransferObject;

final readonly class IcoPurchaseData implements DataTransferObject
{
    public function __construct(
        public int $stockPairId,
        public string $amount,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            stockPairId: (int) $data['stock_pair_id'],
            amount: (string) $data['amount'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'stock_pair_id' => $this->stockPairId,
            'amount' => $this->amount,
        ];
    }
}
