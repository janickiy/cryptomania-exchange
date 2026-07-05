<?php

namespace App\DTO\Admin;

use App\DTO\DataTransferObject;

final readonly class UserStatusData implements DataTransferObject
{
    public function __construct(
        public int $isEmailVerified,
        public int $isActive,
        public int $isFinancialActive,
        public int $isAccessibleUnderMaintenance,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isEmailVerified: (int) $data['is_email_verified'],
            isActive: (int) $data['is_active'],
            isFinancialActive: (int) $data['is_financial_active'],
            isAccessibleUnderMaintenance: (int) $data['is_accessible_under_maintenance'],
        );
    }

    /**
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'is_email_verified' => $this->isEmailVerified,
            'is_active' => $this->isActive,
            'is_financial_active' => $this->isFinancialActive,
            'is_accessible_under_maintenance' => $this->isAccessibleUnderMaintenance,
        ];
    }
}
