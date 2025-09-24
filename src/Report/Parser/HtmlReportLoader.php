<?php

namespace App\Report\Parser;

use DOMDocument;
use RuntimeException;

final class HtmlReportLoader
{
    public function load(string $filePath): DOMDocument
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new RuntimeException(sprintf('Unable to read report source "%s".', $filePath));
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new RuntimeException(sprintf('Failed to read report source "%s".', $filePath));
        }

        $normalized = preg_replace('/<!--(.|\s)*?-->/', '', $content) ?? '';
        $normalized = str_replace(["\r", "\n"], '', $normalized);

        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML($normalized);
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        return $document;
    }
}
