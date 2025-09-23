<?php

namespace App\Report\Dto;

use JsonSerializable;

final class Inquiry implements JsonSerializable
{
    public function __construct(
        public readonly ?string $business,
        public readonly ?string $type,
        public readonly ?string $date,
        public readonly ?string $bureau
    ) {
    }

    public function toArray(): array
    {
        return [
            'business' => $this->business,
            'type' => $this->type,
            'date' => $this->date,
            'bureau' => $this->bureau,
        ];
    }

    /**
     * @return array<int, string|null>
     */
    public function toRow(): array
    {
        return [$this->business, $this->type, $this->date, $this->bureau];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
