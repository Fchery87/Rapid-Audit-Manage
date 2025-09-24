<?php

namespace App\Report\Dto;

use JsonSerializable;

final class CreditUtilizationSummary implements JsonSerializable
{
    public function __construct(
        public readonly float $transUnionBalance,
        public readonly float $transUnionLimit,
        public readonly float $transUnionPercent,
        public readonly string $transUnionPercentImage,
        public readonly float $experianBalance,
        public readonly float $experianLimit,
        public readonly float $experianPercent,
        public readonly string $experianPercentImage,
        public readonly float $equifaxBalance,
        public readonly float $equifaxLimit,
        public readonly float $equifaxPercent,
        public readonly string $equifaxPercentImage,
        public readonly float $totalBalance,
        public readonly float $totalLimit,
        public readonly float $totalPercent
    ) {
    }

    public function toArray(): array
    {
        return [
            'trans_union_balance' => $this->formatCurrency($this->transUnionBalance),
            'trans_union_limit' => $this->formatCurrency($this->transUnionLimit),
            'trans_union_percent' => $this->formatPercent($this->transUnionPercent),
            'trans_union_percent_img' => $this->transUnionPercentImage,
            'experian_balance' => $this->formatCurrency($this->experianBalance),
            'experian_limit' => $this->formatCurrency($this->experianLimit),
            'experian_percent' => $this->formatPercent($this->experianPercent),
            'experian_percent_img' => $this->experianPercentImage,
            'equifax_balance' => $this->formatCurrency($this->equifaxBalance),
            'equifax_limit' => $this->formatCurrency($this->equifaxLimit),
            'equifax_percent' => $this->formatPercent($this->equifaxPercent),
            'equifax_percent_img' => $this->equifaxPercentImage,
            'total_balance' => $this->formatCurrency($this->totalBalance),
            'total_limit' => $this->formatCurrency($this->totalLimit),
            'total_percent' => $this->formatPercent($this->totalPercent),
        ];
    }

    private function formatCurrency(float $value): string
    {
        return number_format($value, 2, '.', ',');
    }

    private function formatPercent(float $value): float
    {
        return round($value, 2);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
