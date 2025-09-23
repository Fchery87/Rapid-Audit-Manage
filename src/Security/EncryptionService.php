<?php

namespace App\Security;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EncryptionService
{
    private const PREFIX = 'ENC::';
    private const FINGERPRINT_BYTES = 16;
    private const FINGERPRINT_HEX_LENGTH = self::FINGERPRINT_BYTES * 2;

    private string $encryptionKey;
    private string $fingerprintKey;
    private string $legacyNonceKey;

    public function __construct(
        #[Autowire('%env(APP_SECRET)%')] private readonly string $appSecret,
    ) {
        if (!extension_loaded('sodium')) {
            throw new RuntimeException('The libsodium extension is required to encrypt sensitive data.');
        }

        $fingerprintKeyBytes = \defined('SODIUM_CRYPTO_GENERICHASH_KEYBYTES') ? SODIUM_CRYPTO_GENERICHASH_KEYBYTES : 32;

        $this->encryptionKey = sodium_crypto_generichash($this->appSecret . ':encryption-key', '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        $this->fingerprintKey = sodium_crypto_generichash($this->appSecret . ':fingerprint-key', '', $fingerprintKeyBytes);
        $this->legacyNonceKey = sodium_crypto_generichash($this->appSecret . ':nonce-key', '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    }

    public function encrypt(string $plaintext): string
    {
        $plaintext = (string) $plaintext;
        if ($plaintext === '') {
            return $plaintext;
        }

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox($plaintext, $nonce, $this->encryptionKey);

        $fingerprint = $this->createFingerprint($plaintext);

        return self::PREFIX . $fingerprint . ':' . base64_encode($nonce . $ciphertext);
    }

    public function decrypt(string $value): string
    {
        if ($value === '') {
            return $value;
        }

        if (!$this->isEncrypted($value)) {
            return $value;
        }

        $encoded = substr($value, strlen(self::PREFIX));

        $fingerprint = null;
        $payload = $encoded;

        $separatorPosition = strpos($encoded, ':');
        if ($separatorPosition !== false) {
            $possibleFingerprint = substr($encoded, 0, $separatorPosition);
            if ($this->isFingerprint($possibleFingerprint)) {
                $fingerprint = $possibleFingerprint;
                $payload = substr($encoded, $separatorPosition + 1);
            }
        }

        $decoded = base64_decode($payload, true);
        if ($decoded === false || strlen($decoded) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            throw new RuntimeException('Unable to decode encrypted payload.');
        }

        $nonce = substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $this->encryptionKey);
        if ($plaintext === false) {
            throw new RuntimeException('Unable to decrypt payload with the configured key.');
        }

        if ($fingerprint !== null) {
            $expectedFingerprint = $this->createFingerprint($plaintext);
            if (!hash_equals($fingerprint, $expectedFingerprint)) {
                throw new RuntimeException('Encrypted payload fingerprint mismatch.');
            }
        }

        return $plaintext;
    }

    public function isEncrypted(string $value): bool
    {
        return str_starts_with($value, self::PREFIX);
    }

    /**
     * @return array{pattern: string, legacy: string}
     */
    public function buildLookupValues(string $plaintext): array
    {
        $plaintext = (string) $plaintext;
        if ($plaintext === '') {
            return ['pattern' => '', 'legacy' => ''];
        }

        $fingerprint = $this->createFingerprint($plaintext);

        return [
            'pattern' => self::PREFIX . $fingerprint . ':%',
            'legacy' => $this->legacyEncrypt($plaintext),
        ];
    }

    private function createFingerprint(string $plaintext): string
    {
        $hash = sodium_crypto_generichash($plaintext, $this->fingerprintKey, self::FINGERPRINT_BYTES);

        return bin2hex($hash);
    }

    private function isFingerprint(string $value): bool
    {
        return strlen($value) === self::FINGERPRINT_HEX_LENGTH && ctype_xdigit($value);
    }

    private function legacyEncrypt(string $plaintext): string
    {
        $nonce = $this->deterministicNonce($plaintext);
        $ciphertext = sodium_crypto_secretbox($plaintext, $nonce, $this->encryptionKey);

        return self::PREFIX . base64_encode($nonce . $ciphertext);
    }

    private function deterministicNonce(string $plaintext): string
    {
        return substr(
            sodium_crypto_generichash($this->legacyNonceKey . $plaintext, '', SODIUM_CRYPTO_SECRETBOX_NONCEBYTES),
            0,
            SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
        );
    }
}
