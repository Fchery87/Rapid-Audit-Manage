<?php

namespace App\Report\Dto;

use JsonSerializable;

final class DerogatoryAccount implements JsonSerializable
{
    public function __construct(
        public readonly ?string $account,
        public readonly ?string $uniqueStatus,
        public readonly ?string $transUnionAccountStatus,
        public readonly ?string $transUnionAccountDate,
        public readonly ?string $transUnionPaymentStatus,
        public readonly ?string $experianAccountStatus,
        public readonly ?string $experianAccountDate,
        public readonly ?string $experianPaymentStatus,
        public readonly ?string $equifaxAccountStatus,
        public readonly ?string $equifaxAccountDate,
        public readonly ?string $equifaxPaymentStatus
    ) {
    }

    public function toArray(): array
    {
        return [
            'account' => $this->account,
            'unique_status' => $this->uniqueStatus,
            'trans_union_account_status' => $this->transUnionAccountStatus,
            'trans_union_account_date' => $this->transUnionAccountDate,
            'trans_union_payment_status' => $this->transUnionPaymentStatus,
            'experian_account_status' => $this->experianAccountStatus,
            'experian_account_date' => $this->experianAccountDate,
            'experian_payment_status' => $this->experianPaymentStatus,
            'equifax_account_status' => $this->equifaxAccountStatus,
            'equifax_account_date' => $this->equifaxAccountDate,
            'equifax_payment_status' => $this->equifaxPaymentStatus,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
