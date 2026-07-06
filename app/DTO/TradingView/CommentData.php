<?php

namespace App\DTO\TradingView;

use App\DTO\DataTransferObject;

final readonly class CommentData implements DataTransferObject
{
    /**
     * Purpose: initializes comment data for a trading view post.
     *
     * Action: carries validated comment content and author id between service and repository layers.
     */
    public function __construct(
        public string $content,
        public int|string $userId,
    ) {
    }

    /**
     * Purpose: creates a DTO from validated input.
     *
     * Action: normalizes comment content before it is persisted.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            content: trim((string) $data['content']),
            userId: $data['user_id'],
        );
    }

    /**
     * Purpose: converts comment data to repository-ready attributes.
     *
     * Action: provides the shape expected by the polymorphic comments relation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'user_id' => $this->userId,
        ];
    }
}
