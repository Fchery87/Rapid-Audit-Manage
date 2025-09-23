<?php

namespace App\Report\Parser;

final class ValueSanitizer
{
    public function collapseWhitespace(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', $value);

        return $value === null ? null : trim($value);
    }

    public function sanitizeLabel(?string $value): ?string
    {
        $value = $this->collapseWhitespace($value);

        return $value === null ? null : trim($value);
    }

    public function toNumber(?string $value): float
    {
        if ($value === null) {
            return 0.0;
        }

        $filtered = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if ($filtered === false || $filtered === null || $filtered === '') {
            return 0.0;
        }

        return (float) $filtered;
    }
}
