<?php

namespace App\DTO\Wallet;

use App\DTO\DataTransferObject;

final readonly class WithdrawalData implements DataTransferObject
{
    public function __construct(
        public string $amount,
        public string $address,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: (string) $data['amount'],
            address: (string) $data['address'],
        );
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'address' => $this->address,
        ];
    }
}
