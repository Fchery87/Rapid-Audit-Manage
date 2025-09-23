<?php

namespace App\Report\Dto;

use JsonSerializable;

final class PublicRecord implements JsonSerializable
{
    public function __construct(
        public readonly ?string $type,
        public readonly ?string $status,
        public readonly ?string $transUnionFiled,
        public readonly ?string $experianFiled,
        public readonly ?string $equifaxFiled
    ) {
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'status' => $this->status,
            'trans_union_files' => $this->transUnionFiled,
            'experian_filed' => $this->experianFiled,
            'equifax_filed' => $this->equifaxFiled,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
