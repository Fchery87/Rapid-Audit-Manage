<?php

declare(strict_types=1);

namespace App\Tests\Parser;

use App\Report\Parser\CreditUtilizationExtractor;
use App\Report\Parser\DerogatoryAccountExtractor;
use App\Report\Parser\HtmlReportLoader;
use App\Report\Parser\InquiryExtractor;
use App\Report\Parser\PersonalInfoExtractor;
use App\Report\Parser\PublicRecordExtractor;
use App\Report\Parser\ReportMetadataFactory;
use App\Report\Parser\ValueSanitizer;
use App\Service\ReportParser;
use PHPUnit\Framework\TestCase;

final class ReportParserSnapshotTest extends TestCase
{
    private const FIXTURE = __DIR__ . '/../Fixtures/reports/identityiq-sample.html';
    private const SNAPSHOT = __DIR__ . '/../Fixtures/reports/identityiq-sample.snapshot.json';

    public function testParserOutputMatchesSnapshot(): void
    {
        $sanitizer = new ValueSanitizer();
        $parser = new ReportParser(
            new HtmlReportLoader(),
            new PersonalInfoExtractor($sanitizer),
            new DerogatoryAccountExtractor($sanitizer),
            new InquiryExtractor($sanitizer),
            new PublicRecordExtractor($sanitizer),
            new CreditUtilizationExtractor($sanitizer),
            new ReportMetadataFactory()
        );

        $result = $parser->parse(self::FIXTURE)->toArray();
        $snapshot = json_decode((string) file_get_contents(self::SNAPSHOT), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame($snapshot['meta']['parser_version'], $result['meta']['parser_version']);

        // Normalise dynamic attributes that change across runs
        $result['meta']['parsed_at'] = $snapshot['meta']['parsed_at'];
        $result['meta']['checksum'] = $snapshot['meta']['checksum'];
        $result['meta']['source_path'] = $snapshot['meta']['source_path'];

        self::assertEquals($snapshot, $result, 'Parsed report should remain stable for known fixture.');
    }
}
