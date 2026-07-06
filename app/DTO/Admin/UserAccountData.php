<?php

namespace App\DTO\Admin;

use App\DTO\DataTransferObject;

final readonly class UserAccountData implements DataTransferObject
{
    /**
     * Purpose: initializes the UserAccountData instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $address = null,
        public ?int $roleId = null,
        public ?string $email = null,
        public ?string $username = null,
        public ?int $isEmailVerified = null,
        public ?int $isFinancialActive = null,
        public ?int $isActive = null,
        public ?int $isAccessibleUnderMaintenance = null,
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
            firstName: trim((string) $data['first_name']),
            lastName: trim((string) $data['last_name']),
            address: isset($data['address']) ? trim((string) $data['address']) : null,
            roleId: isset($data['user_role_management_id']) ? (int) $data['user_role_management_id'] : null,
            email: isset($data['email']) ? trim((string) $data['email']) : null,
            username: isset($data['username']) ? trim((string) $data['username']) : null,
            isEmailVerified: isset($data['is_email_verified']) ? (int) $data['is_email_verified'] : null,
            isFinancialActive: isset($data['is_financial_active']) ? (int) $data['is_financial_active'] : null,
            isActive: isset($data['is_active']) ? (int) $data['is_active'] : null,
            isAccessibleUnderMaintenance: isset($data['is_accessible_under_maintenance']) ? (int) $data['is_accessible_under_maintenance'] : null,
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
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'address' => $this->address,
            'user_role_management_id' => $this->roleId,
            'email' => $this->email,
            'username' => $this->username,
            'is_email_verified' => $this->isEmailVerified,
            'is_financial_active' => $this->isFinancialActive,
            'is_active' => $this->isActive,
            'is_accessible_under_maintenance' => $this->isAccessibleUnderMaintenance,
        ], static fn ($value) => $value !== null);
    }
}
