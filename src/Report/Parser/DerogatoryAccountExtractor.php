<?php

namespace App\Report\Parser;

use App\Report\Dto\DerogatoryAccount;
use App\Report\Dto\DerogatoryAccountCollection;
use DOMDocument;

final class DerogatoryAccountExtractor
{
    /**
     * @var string[]
     */
    private array $lateKeywords = [
        'Collection/Chargeoff',
        'Late 30',
        'Late 60',
        'Late 90',
        'Late 120',
        'Late 150',
        'Late 180',
    ];

    public function __construct(private readonly ValueSanitizer $sanitizer)
    {
    }

    public function extract(DOMDocument $document, int $limit): DerogatoryAccountCollection
    {
        $accounts = [];
        $totalMatches = 0;

        $histories = $document->getElementsByTagName('address-history');
        foreach ($histories as $history) {
            foreach ($history->getElementsByTagName('table') as $table) {
                if ($table->getAttribute('class') !== 'crPrint ng-scope') {
                    continue;
                }

                $accountName = null;
                foreach ($table->getElementsByTagName('div') as $header) {
                    if ($header->getAttribute('class') === 'sub_header ng-binding ng-scope') {
                        $accountName = $this->sanitizer->collapseWhitespace($header->nodeValue);
                    }
                }

                foreach ($table->getElementsByTagName('table') as $dataTable) {
                    if ($dataTable->getAttribute('class') !== 'rpt_content_table rpt_content_header rpt_table4column ng-scope') {
                        continue;
                    }

                    $rowIndex = 0;
                    $tableData = [
                        'account' => $accountName,
                        'trans_union_account_status' => null,
                        'trans_union_account_date' => null,
                        'trans_union_payment_status' => null,
                        'experian_account_status' => null,
                        'experian_account_date' => null,
                        'experian_payment_status' => null,
                        'equifax_account_status' => null,
                        'equifax_account_date' => null,
                        'equifax_payment_status' => null,
                    ];

                    foreach ($dataTable->getElementsByTagName('tr') as $row) {
                        $columnIndex = 0;
                        foreach ($row->getElementsByTagName('td') as $column) {
                            $value = $this->sanitizer->collapseWhitespace($column->nodeValue);

                            if ($rowIndex === 5) {
                                if ($columnIndex === 1) {
                                    $tableData['trans_union_account_status'] = $this->sanitizer->sanitizeLabel($value);
                                } elseif ($columnIndex === 2) {
                                    $tableData['experian_account_status'] = $this->sanitizer->sanitizeLabel($value);
                                } elseif ($columnIndex === 3) {
                                    $tableData['equifax_account_status'] = $this->sanitizer->sanitizeLabel($value);
                                }
                            }

                            if ($rowIndex === 7) {
                                if ($columnIndex === 1) {
                                    $tableData['trans_union_account_date'] = $value;
                                } elseif ($columnIndex === 2) {
                                    $tableData['experian_account_date'] = $value;
                                } elseif ($columnIndex === 3) {
                                    $tableData['equifax_account_date'] = $value;
                                }
                            }

                            if ($rowIndex === 13) {
                                if ($columnIndex === 1) {
                                    $tableData['trans_union_payment_status'] = $value;
                                } elseif ($columnIndex === 2) {
                                    $tableData['experian_payment_status'] = $value;
                                } elseif ($columnIndex === 3) {
                                    $tableData['equifax_payment_status'] = $value;
                                }
                            }

                            ++$columnIndex;
                        }
                        ++$rowIndex;
                    }

                    $shouldAdd = false;

                    if ($this->isDerogatory($tableData['trans_union_account_status'], $tableData['trans_union_payment_status'])) {
                        ++$totalMatches;
                        $shouldAdd = true;
                    }

                    if ($this->isDerogatory($tableData['experian_account_status'], $tableData['experian_payment_status'])) {
                        ++$totalMatches;
                        $shouldAdd = true;
                    }

                    if ($this->isDerogatory($tableData['equifax_account_status'], $tableData['equifax_payment_status'])) {
                        ++$totalMatches;
                        $shouldAdd = true;
                    }

                    if ($shouldAdd === false) {
                        continue;
                    }

                    $uniqueStatus = $this->buildUniqueStatus([
                        $tableData['trans_union_payment_status'],
                        $tableData['experian_payment_status'],
                        $tableData['equifax_payment_status'],
                    ]);

                    if (count($accounts) < $limit) {
                        $accounts[] = new DerogatoryAccount(
                            $tableData['account'],
                            $uniqueStatus,
                            $tableData['trans_union_account_status'],
                            $tableData['trans_union_account_date'],
                            $tableData['trans_union_payment_status'],
                            $tableData['experian_account_status'],
                            $tableData['experian_account_date'],
                            $tableData['experian_payment_status'],
                            $tableData['equifax_account_status'],
                            $tableData['equifax_account_date'],
                            $tableData['equifax_payment_status']
                        );
                    }
                }
            }
        }

        return new DerogatoryAccountCollection($totalMatches, $limit, $accounts);
    }

    private function isDerogatory(?string $accountStatus, ?string $paymentStatus): bool
    {
        if ($accountStatus === 'Derogatory') {
            return true;
        }

        if ($paymentStatus === null || $paymentStatus === '') {
            return false;
        }

        foreach ($this->lateKeywords as $keyword) {
            if (stripos($paymentStatus, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, string|null> $statuses
     */
    private function buildUniqueStatus(array $statuses): ?string
    {
        $normalized = array_filter(array_map(
            fn (?string $status) => $this->sanitizer->collapseWhitespace($status),
            $statuses
        ));

        if ($normalized === []) {
            return null;
        }

        return implode(', ', array_unique($normalized));
    }
}
