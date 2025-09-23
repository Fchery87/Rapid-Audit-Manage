<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Security\EncryptionService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class EncryptionServiceTest extends TestCase
{
    private EncryptionService $service;

    protected function setUp(): void
    {
        $this->service = new EncryptionService('test-app-secret');
    }

    public function testEncryptAndDecryptRoundTrip(): void
    {
        $plaintext = 'Sensitive-Value-123';

        $ciphertext = $this->service->encrypt($plaintext);

        self::assertNotSame($plaintext, $ciphertext);
        self::assertTrue($this->service->isEncrypted($ciphertext));
        self::assertSame($plaintext, $this->service->decrypt($ciphertext));
    }

    public function testBuildLookupValuesIncludesLegacyCiphertext(): void
    {
        $lookup = $this->service->buildLookupValues('user@example.com');

        self::assertStringEndsWith('%', $lookup['pattern']);
        self::assertTrue($this->service->isEncrypted($lookup['legacy']));
        self::assertSame('user@example.com', $this->service->decrypt($lookup['legacy']));
    }

    public function testDecryptLegacyCiphertextGeneratedElsewhere(): void
    {
        $legacy = $this->service->buildLookupValues('987654321')['legacy'];

        self::assertSame('987654321', $this->service->decrypt($legacy));
    }

    public function testTamperingWithFingerprintIsDetected(): void
    {
        $ciphertext = $this->service->encrypt('value-to-protect');
        $tampered = substr_replace(
            $ciphertext,
            str_repeat('a', 32),
            strlen('ENC::'),
            32
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('fingerprint mismatch');

        $this->service->decrypt($tampered);
    }
}
