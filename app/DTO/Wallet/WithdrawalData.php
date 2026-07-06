<?php

namespace App\DTO\Wallet;

use App\DTO\DataTransferObject;

final readonly class WithdrawalData implements DataTransferObject
{
    /**
     * Purpose: initializes the WithdrawalData instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        public string $amount,
        public string $address,
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
            amount: (string) $data['amount'],
            address: (string) $data['address'],
        );
    }

    /**
     * Purpose: converts the DTO back to an array.
     *
     * Action: provides data to repositories, models, or APIs in the expected format.
     *
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
