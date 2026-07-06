<?php

namespace App\DTO\Admin;

use App\DTO\DataTransferObject;

final readonly class UserRoleManagementData implements DataTransferObject
{
    /**
     * Purpose: initializes the UserRoleManagementData instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     * @param array<string, mixed> $roles
     */
    public function __construct(public string $roleName, public array $roles)
    {
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
            roleName: trim((string) $data['role_name']),
            roles: $data['roles'] ?? [],
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
        return [
            'role_name' => $this->roleName,
            'route_group' => $this->roles,
        ];
    }
}
