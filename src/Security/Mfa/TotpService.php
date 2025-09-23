<?php

namespace App\Security\Mfa;

use RuntimeException;

class TotpService
{
    private const CODE_DIGITS = 6;
    private const PERIOD = 30;

    public function generateSecret(int $length = 20): string
    {
        return $this->base32Encode(random_bytes($length));
    }

    public function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);
        if ($code === '' || !ctype_digit($code) || strlen($code) !== self::CODE_DIGITS) {
            return false;
        }

        $binarySecret = $this->base32Decode($secret);
        if ($binarySecret === null) {
            throw new RuntimeException('Invalid MFA secret.');
        }

        $timeSlice = (int) floor(time() / self::PERIOD);

        for ($i = -$window; $i <= $window; $i++) {
            $calculated = $this->calculateCode($binarySecret, $timeSlice + $i);
            if (hash_equals($calculated, $code)) {
                return true;
            }
        }

        return false;
    }

    private function calculateCode(string $secret, int $timeSlice): string
    {
        $time = pack('N*', 0, $timeSlice);
        $hash = hash_hmac('sha1', $time, $secret, true);

        $offset = ord(substr($hash, -1)) & 0x0F;
        $segment = substr($hash, $offset, 4);

        $value = unpack('N', $segment)[1] & 0x7FFFFFFF;
        $code = $value % (10 ** self::CODE_DIGITS);

        return str_pad((string) $code, self::CODE_DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $encoded = '';
        $current = 0;
        $bits = 0;

        foreach (array_map('ord', str_split($data)) as $value) {
            $current = ($current << 8) | $value;
            $bits += 8;

            while ($bits >= 5) {
                $bits -= 5;
                $encoded .= $alphabet[($current >> $bits) & 0x1F];
            }
        }

        if ($bits > 0) {
            $encoded .= $alphabet[($current << (5 - $bits)) & 0x1F];
        }

        while (strlen($encoded) % 8 !== 0) {
            $encoded .= '=';
        }

        return $encoded;
    }

    private function base32Decode(string $value): ?string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $value = strtoupper($value);
        $value = preg_replace('/[^A-Z2-7=]/', '', $value) ?? '';
        $value = rtrim($value, '=');

        $bits = 0;
        $current = 0;
        $output = '';

        for ($i = 0, $length = strlen($value); $i < $length; $i++) {
            $char = $value[$i];
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                return null;
            }

            $current = ($current << 5) | $pos;
            $bits += 5;

            if ($bits >= 8) {
                $bits -= 8;
                $output .= chr(($current >> $bits) & 0xFF);
            }
        }

        return $output;
    }
}
