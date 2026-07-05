<?php

namespace App\DTO\Admin;

use App\DTO\DataTransferObject;

final readonly class SystemNoticeData implements DataTransferObject
{
    public function __construct(
        public string $title,
        public string $description,
        public string $startAt,
        public string $endAt,
        public int|string $status,
        public ?string $type = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: trim((string) $data['title']),
            description: trim((string) $data['description']),
            startAt: (string) $data['start_at'],
            endAt: (string) $data['end_at'],
            status: $data['status'],
            type: isset($data['type']) ? (string) $data['type'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'start_at' => $this->startAt,
            'end_at' => $this->endAt,
            'status' => $this->status,
            'type' => $this->type,
        ], static fn ($value) => $value !== null);
    }
}
