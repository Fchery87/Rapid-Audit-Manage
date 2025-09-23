<?php

namespace App\Report\Dto;

use DateTimeImmutable;
use JsonSerializable;

final class ReportProvenance implements JsonSerializable
{
    /**
     * @param SectionProvenance[] $sections
     */
    public function __construct(
        public readonly string $sourceName,
        public readonly string $sourcePath,
        public readonly string $checksum,
        public readonly int $filesize,
        public readonly DateTimeImmutable $parsedAt,
        public readonly string $parserVersion,
        public readonly array $sections
    ) {
    }

    /**
     * @return SectionProvenance[]
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    public function toArray(): array
    {
        return [
            'source_name' => $this->sourceName,
            'source_path' => $this->sourcePath,
            'checksum' => $this->checksum,
            'filesize' => $this->filesize,
            'parsed_at' => $this->parsedAt->format(DateTimeImmutable::ATOM),
            'parser_version' => $this->parserVersion,
            'sections' => array_map(static fn(SectionProvenance $section) => $section->toArray(), $this->sections),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
