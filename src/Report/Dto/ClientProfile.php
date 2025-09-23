<?php

namespace App\Report\Dto;

use JsonSerializable;

final class ClientProfile implements JsonSerializable
{
    public function __construct(
        public readonly BureauProfile $transUnion,
        public readonly BureauProfile $experian,
        public readonly BureauProfile $equifax
    ) {
    }

    public function toArray(): array
    {
        return [
            'trans_union' => $this->transUnion->toArray(),
            'experian' => $this->experian->toArray(),
            'equifax' => $this->equifax->toArray(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
