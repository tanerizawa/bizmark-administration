<?php

namespace App\Services\Security;

use RuntimeException;

class TotpService
{
    public function generateSecret(int $bytes = 20): string
    {
        return $this->base32Encode(random_bytes($bytes));
    }

    public function verify(string $base32Secret, string $code, ?int $timestamp = null, int $window = 1): bool
    {
        $timestamp ??= time();
        $code = preg_replace('/\s+/', '', $code) ?? '';
        if (! ctype_digit($code) || strlen($code) !== 6) {
            return false;
        }

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->at($base32Secret, $timestamp + ($i * 30)), $code)) {
                return true;
            }
        }

        return false;
    }

    public function at(string $base32Secret, int $timestamp): string
    {
        $secret = $this->base32Decode($base32Secret);
        $counter = intdiv($timestamp, 30);

        $binCounter = pack('N*', 0).pack('N*', $counter);
        $hash = hash_hmac('sha1', $binCounter, $secret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $part = substr($hash, $offset, 4);
        $value = unpack('N', $part)[1] & 0x7FFFFFFF;
        $otp = $value % 1000000;

        return str_pad((string) $otp, 6, '0', STR_PAD_LEFT);
    }

    public function otpauthUrl(string $label, string $issuer, string $base32Secret): string
    {
        $label = rawurlencode($label);
        $issuerEnc = rawurlencode($issuer);

        return "otpauth://totp/{$label}?secret={$base32Secret}&issuer={$issuerEnc}&algorithm=SHA1&digits=6&period=30";
    }

    private function base32Decode(string $base32): string
    {
        $base32 = strtoupper(preg_replace('/[^A-Z2-7]/', '', $base32) ?? '');
        if ($base32 === '') {
            throw new RuntimeException('Invalid base32 secret');
        }

        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        for ($i = 0; $i < strlen($base32); $i++) {
            $val = strpos($alphabet, $base32[$i]);
            if ($val === false) {
                throw new RuntimeException('Invalid base32 secret');
            }
            $bits .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
        }

        $bytes = '';
        for ($i = 0; $i + 8 <= strlen($bits); $i += 8) {
            $bytes .= chr(bindec(substr($bits, $i, 8)));
        }

        return $bytes;
    }

    private function base32Encode(string $bytes): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        for ($i = 0; $i < strlen($bytes); $i++) {
            $bits .= str_pad(decbin(ord($bytes[$i])), 8, '0', STR_PAD_LEFT);
        }

        $out = '';
        for ($i = 0; $i < strlen($bits); $i += 5) {
            $chunk = substr($bits, $i, 5);
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            }
            $out .= $alphabet[bindec($chunk)];
        }

        return $out;
    }
}
