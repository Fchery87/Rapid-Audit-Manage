<?php

namespace App\Report\Parser;

use App\Report\Dto\CreditUtilizationSummary;
use DOMDocument;

final class CreditUtilizationExtractor
{
    public function __construct(private readonly ValueSanitizer $sanitizer)
    {
    }

    public function extract(DOMDocument $document): CreditUtilizationSummary
    {
        $transUnionBalance = 0.0;
        $transUnionLimit = 0.0;
        $experianBalance = 0.0;
        $experianLimit = 0.0;
        $equifaxBalance = 0.0;
        $equifaxLimit = 0.0;

        $histories = $document->getElementsByTagName('address-history');
        foreach ($histories as $history) {
            foreach ($history->getElementsByTagName('table') as $table) {
                if ($table->getAttribute('class') !== 'crPrint ng-scope') {
                    continue;
                }

                foreach ($table->getElementsByTagName('table') as $dataTable) {
                    if ($dataTable->getAttribute('class') !== 'rpt_content_table rpt_content_header rpt_table4column ng-scope') {
                        continue;
                    }

                    $rowIndex = 0;
                    $tableData = [
                        'trans_union_account_type' => null,
                        'experian_account_type' => null,
                        'equifax_account_type' => null,
                        'trans_union_account_status' => null,
                        'experian_account_status' => null,
                        'equifax_account_status' => null,
                        'trans_union_balance' => 0.0,
                        'experian_balance' => 0.0,
                        'equifax_balance' => 0.0,
                        'trans_union_limit' => 0.0,
                        'experian_limit' => 0.0,
                        'equifax_limit' => 0.0,
                    ];

                    foreach ($dataTable->getElementsByTagName('tr') as $row) {
                        $columnIndex = 0;
                        foreach ($row->getElementsByTagName('td') as $column) {
                            $value = $this->sanitizer->collapseWhitespace($column->nodeValue);

                            if ($rowIndex === 2) {
                                if ($columnIndex === 1) {
                                    $tableData['trans_union_account_type'] = $this->sanitizer->sanitizeLabel($value);
                                } elseif ($columnIndex === 2) {
                                    $tableData['experian_account_type'] = $this->sanitizer->sanitizeLabel($value);
                                } elseif ($columnIndex === 3) {
                                    $tableData['equifax_account_type'] = $this->sanitizer->sanitizeLabel($value);
                                }
                            }

                            if ($rowIndex === 5) {
                                if ($columnIndex === 1) {
                                    $tableData['trans_union_account_status'] = $this->sanitizer->sanitizeLabel($value);
                                } elseif ($columnIndex === 2) {
                                    $tableData['experian_account_status'] = $this->sanitizer->sanitizeLabel($value);
                                } elseif ($columnIndex === 3) {
                                    $tableData['equifax_account_status'] = $this->sanitizer->sanitizeLabel($value);
                                }
                            }

                            if ($rowIndex === 8) {
                                if ($columnIndex === 1) {
                                    $tableData['trans_union_balance'] = $this->sanitizer->toNumber($value);
                                } elseif ($columnIndex === 2) {
                                    $tableData['experian_balance'] = $this->sanitizer->toNumber($value);
                                } elseif ($columnIndex === 3) {
                                    $tableData['equifax_balance'] = $this->sanitizer->toNumber($value);
                                }
                            }

                            if ($rowIndex === 11) {
                                if ($columnIndex === 1) {
                                    $tableData['trans_union_limit'] = $this->sanitizer->toNumber($value);
                                } elseif ($columnIndex === 2) {
                                    $tableData['experian_limit'] = $this->sanitizer->toNumber($value);
                                } elseif ($columnIndex === 3) {
                                    $tableData['equifax_limit'] = $this->sanitizer->toNumber($value);
                                }
                            }

                            ++$columnIndex;
                        }
                        ++$rowIndex;
                    }

                    if ($this->isOpenRevolving($tableData['trans_union_account_type'], $tableData['trans_union_account_status'])) {
                        $transUnionBalance += $tableData['trans_union_balance'];
                        $transUnionLimit += $tableData['trans_union_limit'];
                    }

                    if ($this->isOpenRevolving($tableData['experian_account_type'], $tableData['experian_account_status'])) {
                        $experianBalance += $tableData['experian_balance'];
                        $experianLimit += $tableData['experian_limit'];
                    }

                    if ($this->isOpenRevolving($tableData['equifax_account_type'], $tableData['equifax_account_status'])) {
                        $equifaxBalance += $tableData['equifax_balance'];
                        $equifaxLimit += $tableData['equifax_limit'];
                    }
                }
            }
        }

        $transUnionPercent = $this->calculatePercent($transUnionBalance, $transUnionLimit);
        $experianPercent = $this->calculatePercent($experianBalance, $experianLimit);
        $equifaxPercent = $this->calculatePercent($equifaxBalance, $equifaxLimit);

        $totalBalance = ($transUnionBalance + $experianBalance + $equifaxBalance) / 3;
        $totalLimit = ($transUnionLimit + $experianLimit + $equifaxLimit) / 3;
        $totalPercent = $this->calculatePercent($totalBalance, $totalLimit);

        return new CreditUtilizationSummary(
            $transUnionBalance,
            $transUnionLimit,
            $transUnionPercent,
            $this->percentToImage($transUnionPercent),
            $experianBalance,
            $experianLimit,
            $experianPercent,
            $this->percentToImage($experianPercent),
            $equifaxBalance,
            $equifaxLimit,
            $equifaxPercent,
            $this->percentToImage($equifaxPercent),
            $totalBalance,
            $totalLimit,
            $totalPercent
        );
    }

    private function isOpenRevolving(?string $type, ?string $status): bool
    {
        return $type === 'Revolving' && $status === 'Open';
    }

    private function calculatePercent(float $balance, float $limit): float
    {
        if ($limit <= 0.0) {
            return 0.0;
        }

        return ($balance / $limit) * 100;
    }

    private function percentToImage(float $percent): string
    {
        if ($percent >= 75) {
            return 'credit-utilization-very-poor.jpg';
        }

        if ($percent >= 50) {
            return 'credit-utilization-poor.jpg';
        }

        if ($percent >= 30) {
            return 'credit-utilization-fair.jpg';
        }

        if ($percent >= 10) {
            return 'credit-utilization-good.jpg';
        }

        if ($percent >= 0) {
            return 'credit-utilization-excellent.jpg';
        }

        return 'credit-utilization-no-data.jpg';
    }
}
