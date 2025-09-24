<?php

namespace App\Report\Parser;

use App\Report\Dto\Inquiry;
use App\Report\Dto\InquiryCollection;
use DOMDocument;

final class InquiryExtractor
{
    public function __construct(private readonly ValueSanitizer $sanitizer)
    {
    }

    public function extract(DOMDocument $document, int $limit): InquiryCollection
    {
        $records = [];
        $total = 0;

        $inquiries = $document->getElementById('Inquiries');
        if ($inquiries === null) {
            return new InquiryCollection(0, $limit, []);
        }

        foreach ($inquiries->getElementsByTagName('table') as $table) {
            if ($table->getAttribute('class') !== 'rpt_content_table rpt_content_header rpt_content_contacts ng-scope') {
                continue;
            }

            $rowIndex = 0;
            foreach ($table->getElementsByTagName('tr') as $row) {
                if ($rowIndex === 0) {
                    ++$rowIndex;
                    continue;
                }

                $cells = [];
                foreach ($row->getElementsByTagName('td') as $column) {
                    $cells[] = $this->sanitizer->collapseWhitespace($column->nodeValue);
                }

                ++$total;
                if (count($records) < $limit) {
                    [$business, $type, $date, $bureau] = array_pad($cells, 4, null);
                    $records[] = new Inquiry($business, $type, $date, $bureau);
                }

                ++$rowIndex;
            }
        }

        return new InquiryCollection($total, $limit, $records);
    }
}
