<?php

namespace App\DTO\Admin;

use App\DTO\DataTransferObject;

final readonly class WalletBalanceData implements DataTransferObject
{
    public function __construct(public string $amount)
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(amount: (string) $data['amount']);
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return ['amount' => $this->amount];
    }
}
