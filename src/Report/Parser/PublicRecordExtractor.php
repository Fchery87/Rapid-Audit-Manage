<?php

namespace App\Report\Parser;

use App\Report\Dto\PublicRecord;
use App\Report\Dto\PublicRecordCollection;
use DOMDocument;

final class PublicRecordExtractor
{
    public function __construct(private readonly ValueSanitizer $sanitizer)
    {
    }

    public function extract(DOMDocument $document, int $limit): PublicRecordCollection
    {
        $records = [];
        $total = 0;

        $root = $document->getElementById('PublicInformation');
        if ($root === null) {
            return new PublicRecordCollection(0, $limit, []);
        }

        foreach ($root->getElementsByTagName('ng') as $ng) {
            foreach ($ng->getElementsByTagName('div') as $div) {
                $class = $div->getAttribute('class');
                if (str_contains($class, 'ng-hide') || str_contains($class, 'sub_header')) {
                    continue;
                }

                foreach ($div->getElementsByTagName('table') as $table) {
                    if ($table->getAttribute('class') !== 'rpt_content_table rpt_content_header rpt_table4column') {
                        continue;
                    }

                    $type = null;
                    $status = null;
                    $transUnionFiled = null;
                    $experianFiled = null;
                    $equifaxFiled = null;

                    $rowIndex = 0;
                    foreach ($table->getElementsByTagName('tr') as $row) {
                        $columnIndex = 0;
                        foreach ($row->getElementsByTagName('td') as $column) {
                            $value = $this->sanitizer->collapseWhitespace($column->nodeValue);

                            if ($rowIndex === 1 && $type === null && $value !== null && $value !== '') {
                                $type = $value;
                            }

                            if ($rowIndex === 2 && $status === null && $value !== null && $value !== '') {
                                $status = $value;
                            }

                            if ($rowIndex === 3) {
                                if ($columnIndex === 1) {
                                    $transUnionFiled = $value;
                                } elseif ($columnIndex === 2) {
                                    $experianFiled = $value;
                                } elseif ($columnIndex === 3) {
                                    $equifaxFiled = $value;
                                }
                            }

                            ++$columnIndex;
                        }
                        ++$rowIndex;
                    }

                    if ($type === null) {
                        continue;
                    }

                    ++$total;
                    if (count($records) < $limit) {
                        $records[] = new PublicRecord($type, $status, $transUnionFiled, $experianFiled, $equifaxFiled);
                    }
                }
            }
        }

        return new PublicRecordCollection($total, $limit, $records);
    }
}
