<?php

namespace App\Report\Dto;

use JsonSerializable;

final class BureauProfile implements JsonSerializable
{
    public function __construct(
        public readonly ?string $reportDate,
        public readonly ?string $name,
        public readonly ?string $alsoKnownAs,
        public readonly ?string $formerName,
        public readonly ?string $dateOfBirth,
        public readonly ?string $currentAddress,
        public readonly ?string $previousAddress,
        public readonly ?string $employers,
        public readonly ?string $creditScore,
        public readonly ?string $lendingRank,
        public readonly ?string $scoreScale,
        public readonly ?string $totalAccounts,
        public readonly ?string $openAccounts,
        public readonly ?string $closedAccounts,
        public readonly ?string $delinquent,
        public readonly ?string $derogatory,
        public readonly ?string $collection,
        public readonly ?string $balances,
        public readonly ?string $payments,
        public readonly ?string $publicRecords,
        public readonly ?string $inquiries
    ) {
    }

    public function toArray(): array
    {
        return [
            'report_data' => $this->reportDate,
            'name' => $this->name,
            'also_known_as' => $this->alsoKnownAs,
            'former_name' => $this->formerName,
            'date_of_birth' => $this->dateOfBirth,
            'current_address' => $this->currentAddress,
            'previous_address' => $this->previousAddress,
            'employers' => $this->employers,
            'credit_score' => $this->creditScore,
            'lending_rank' => $this->lendingRank,
            'score_scale' => $this->scoreScale,
            'total_accounts' => $this->totalAccounts,
            'open_accounts' => $this->openAccounts,
            'closed_accounts' => $this->closedAccounts,
            'delinquent' => $this->delinquent,
            'derogatory' => $this->derogatory,
            'collection' => $this->collection,
            'balances' => $this->balances,
            'payments' => $this->payments,
            'public_records' => $this->publicRecords,
            'inquiries' => $this->inquiries,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
