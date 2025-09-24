<?php

namespace App\Report\Dto;

use JsonSerializable;

final class SectionProvenance implements JsonSerializable
{
    public function __construct(
        public readonly string $name,
        public readonly int $total,
        public readonly int $returned,
        public readonly int $limit
    ) {
    }

    public function isTruncated(): bool
    {
        return $this->total > $this->returned;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'total' => $this->total,
            'returned' => $this->returned,
            'limit' => $this->limit,
            'truncated' => $this->isTruncated(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
