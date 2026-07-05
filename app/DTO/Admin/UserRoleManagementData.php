<?php

namespace App\DTO\Admin;

use App\DTO\DataTransferObject;

final readonly class UserRoleManagementData implements DataTransferObject
{
    /**
     * @param array<string, mixed> $roles
     */
    public function __construct(public string $roleName, public array $roles)
    {
    }

    /**
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
