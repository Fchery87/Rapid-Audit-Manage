<?php

namespace App\Service;

use App\Report\Dto\ParsedReport;
use App\Report\Dto\SectionProvenance;
use App\Report\Parser\CreditUtilizationExtractor;
use App\Report\Parser\DerogatoryAccountExtractor;
use App\Report\Parser\HtmlReportLoader;
use App\Report\Parser\InquiryExtractor;
use App\Report\Parser\PersonalInfoExtractor;
use App\Report\Parser\PublicRecordExtractor;
use App\Report\Parser\ReportMetadataFactory;

final class ReportParser
{
    private const PARSER_VERSION = '2.0.0';
    private const MAX_DEROGATORY_ACCOUNTS = 250;
    private const MAX_INQUIRIES = 250;
    private const MAX_PUBLIC_RECORDS = 250;

    public function __construct(
        private readonly HtmlReportLoader $loader,
        private readonly PersonalInfoExtractor $personalInfoExtractor,
        private readonly DerogatoryAccountExtractor $derogatoryAccountExtractor,
        private readonly InquiryExtractor $inquiryExtractor,
        private readonly PublicRecordExtractor $publicRecordExtractor,
        private readonly CreditUtilizationExtractor $creditUtilizationExtractor,
        private readonly ReportMetadataFactory $metadataFactory,
    ) {
    }

    public function parse(string $filePath): ParsedReport
    {
        $document = $this->loader->load($filePath);

        $clientProfile = $this->personalInfoExtractor->extract($document);
        $derogatory = $this->derogatoryAccountExtractor->extract($document, self::MAX_DEROGATORY_ACCOUNTS);
        $inquiries = $this->inquiryExtractor->extract($document, self::MAX_INQUIRIES);
        $publicRecords = $this->publicRecordExtractor->extract($document, self::MAX_PUBLIC_RECORDS);
        $creditUtilization = $this->creditUtilizationExtractor->extract($document);

        $sections = [
            new SectionProvenance('derogatory_accounts', $derogatory->getTotal(), $derogatory->count(), $derogatory->getLimit()),
            new SectionProvenance('inquiry_accounts', $inquiries->getTotal(), $inquiries->count(), $inquiries->getLimit()),
            new SectionProvenance('public_records', $publicRecords->getTotal(), $publicRecords->count(), $publicRecords->getLimit()),
        ];

        $provenance = $this->metadataFactory->fromFile($filePath, $sections, self::PARSER_VERSION);

        return new ParsedReport(
            $provenance,
            $clientProfile,
            $creditUtilization,
            $derogatory,
            $inquiries,
            $publicRecords
        );
    }

    public function loadReportData(string $filePath): array
    {
        return $this->parse($filePath)->toArray();
    }
}
