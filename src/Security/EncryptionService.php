<?php

namespace App\Security;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EncryptionService
{
    private const PREFIX = 'ENC::';

    private string $encryptionKey;

    public function __construct(
        #[Autowire('%env(APP_SECRET)%')] private readonly string $appSecret,
    ) {
        if (!extension_loaded('sodium')) {
            throw new RuntimeException('The libsodium extension is required to encrypt sensitive data.');
        }

        $this->encryptionKey = sodium_crypto_generichash($this->appSecret . ':encryption-key', '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    }

    public function encrypt(string $plaintext): string
    {
        $plaintext = (string) $plaintext;
        if ($plaintext === '') {
            return $plaintext;
        }

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox($plaintext, $nonce, $this->encryptionKey);

        return self::PREFIX . base64_encode($nonce . $ciphertext);
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
        $decoded = base64_decode($encoded, true);
        if ($decoded === false || strlen($decoded) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            throw new RuntimeException('Unable to decode encrypted payload.');
        }

        $nonce = substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $this->encryptionKey);
        if ($plaintext === false) {
            throw new RuntimeException('Unable to decrypt payload with the configured key.');
        }

        return $plaintext;
    }

    public function isEncrypted(string $value): bool
    {
        return str_starts_with($value, self::PREFIX);
    }

}
