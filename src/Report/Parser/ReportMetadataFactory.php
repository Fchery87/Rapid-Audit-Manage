<?php

namespace App\Report\Parser;

use App\Report\Dto\ReportProvenance;
use App\Report\Dto\SectionProvenance;
use DateTimeImmutable;

final class ReportMetadataFactory
{
    /**
     * @param SectionProvenance[] $sections
     */
    public function fromFile(string $filePath, array $sections, string $parserVersion): ReportProvenance
    {
        $resolvedPath = realpath($filePath) ?: $filePath;
        $checksum = is_readable($filePath) ? hash_file('sha256', $filePath) : '';
        $filesize = is_readable($filePath) ? (int) (filesize($filePath) ?: 0) : 0;

        return new ReportProvenance(
            basename($filePath),
            $resolvedPath,
            $checksum ?: '',
            $filesize,
            new DateTimeImmutable('now'),
            $parserVersion,
            $sections
        );
    }
}
