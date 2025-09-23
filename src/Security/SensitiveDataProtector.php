<?php

namespace App\Security;

class SensitiveDataProtector
{
    /** @var string[] */
    private const ACCOUNT_PROTECTED_FIELDS = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address1',
        'address2',
        'city',
        'state',
        'zip',
        'social',
        'credit_company',
        'credit_company_user',
        'credit_company_password',
        'credit_company_code',
    ];

    public function __construct(private readonly EncryptionService $encryption)
    {
    }

    public function encryptAccountPayload(array $payload): array
    {
        foreach (self::ACCOUNT_PROTECTED_FIELDS as $field) {
            if (!array_key_exists($field, $payload)) {
                continue;
            }

            $value = $payload[$field];
            if ($value === null || $value === '') {
                continue;
            }

            $payload[$field] = $this->encryption->encrypt((string) $value);
        }

        return $payload;
    }

    public function decryptAccountRecord(array $record): array
    {
        foreach (self::ACCOUNT_PROTECTED_FIELDS as $field) {
            if (!array_key_exists($field, $record)) {
                continue;
            }

            $value = $record[$field];
            if ($value === null || $value === '') {
                continue;
            }

            $record[$field] = $this->encryption->decrypt((string) $value);
        }

        return $record;
    }

    public function encryptValue(string $value): string
    {
        return $this->encryption->encrypt($value);
    }

    /**
     * @return array{pattern: string, legacy: string}
     */
    public function buildLookupValues(string $value): array
    {
        return $this->encryption->buildLookupValues($value);
    }

    public function decryptValue(string $value): string
    {
        return $this->encryption->decrypt($value);
    }
}
