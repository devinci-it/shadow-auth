<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\Services;

/**
 * Generates and verifies TOTP secrets and one-time codes.
 */
final class TwoFactorService
{
    public function generateSecret(int $length = 32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        for ($index = 0; $index < $length; $index++) {
            $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $secret;
    }

    /**
     * Verifies a 6-digit TOTP code against the current +/- window.
     */
    public function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $normalized = preg_replace('/\s+/', '', $code) ?? '';

        if (!preg_match('/^\d{6}$/', $normalized)) {
            return false;
        }

        $timeSlice = (int) floor(time() / 30);

        for ($offset = -$window; $offset <= $window; $offset++) {
            $candidate = $this->generateCode($secret, $timeSlice + $offset);
            if (hash_equals($candidate, $normalized)) {
                return true;
            }
        }

        return false;
    }

    private function generateCode(string $secret, int $timeSlice): string
    {
        $key = $this->base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hmac = hash_hmac('sha1', $time, $key, true);

        $offset = ord(substr($hmac, -1)) & 0x0F;
        $value = unpack('N', substr($hmac, $offset, 4))[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $value): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $value = strtoupper($value);

        $binary = '';
        $length = strlen($value);

        for ($index = 0; $index < $length; $index++) {
            $position = strpos($alphabet, $value[$index]);
            if ($position === false) {
                continue;
            }

            $binary .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $output = '';
        for ($index = 0; $index + 8 <= strlen($binary); $index += 8) {
            $output .= chr(bindec(substr($binary, $index, 8)));
        }

        return $output;
    }
}
