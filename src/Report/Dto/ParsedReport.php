<?php

namespace App\Report\Dto;

use JsonSerializable;

final class ParsedReport implements JsonSerializable
{
    public function __construct(
        public readonly ReportProvenance $provenance,
        public readonly ClientProfile $clientProfile,
        public readonly CreditUtilizationSummary $creditUtilization,
        public readonly DerogatoryAccountCollection $derogatoryAccounts,
        public readonly InquiryCollection $inquiries,
        public readonly PublicRecordCollection $publicRecords
    ) {
    }

    public function toArray(): array
    {
        return [
            'meta' => $this->provenance->toArray(),
            'client_data' => $this->clientProfile->toArray(),
            'credit_info' => $this->creditUtilization->toArray(),
            'derogatory_accounts' => $this->derogatoryAccounts->toArray(),
            'inquiry_accounts' => $this->inquiries->toArray(),
            'public_records' => $this->publicRecords->toArray(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
