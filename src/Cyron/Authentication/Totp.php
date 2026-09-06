<?php

namespace Cyron\Authentication;

use Cyron\Database\ModelRegistry;

final class Totp
{
    public static function generateSecret(int $bytes = 20): string
    {
        if ($bytes < 1) throw new \InvalidArgumentException('TOTP secret size must be positive.');
        return self::base32(random_bytes($bytes));
    }

    public static function provisioningUri(string $issuer, string $account, string $secret): string
    {
        return 'otpauth://totp/' . rawurlencode($issuer . ':' . $account)
            . '?secret=' . rawurlencode($secret)
            . '&issuer=' . rawurlencode($issuer)
            . '&algorithm=SHA1&digits=6&period=30';
    }

    public static function code(string $secret, ?int $time = null): string
    {
        $counter = intdiv($time ?? time(), 30);
        $key = self::decode($secret);
        $bin = pack('N*', 0, $counter);
        $hash = hash_hmac('sha1', $bin, $key, true);
        $offset = ord($hash[19]) & 15;
        $number = ((ord($hash[$offset]) & 127) << 24)
            | ((ord($hash[$offset + 1]) & 255) << 16)
            | ((ord($hash[$offset + 2]) & 255) << 8)
            | (ord($hash[$offset + 3]) & 255);
        return str_pad((string) ($number % 1000000), 6, '0', STR_PAD_LEFT);
    }

    public static function verify(string $secret, string $code, int $window = 1): bool
    {
        if ($window < 0) throw new \InvalidArgumentException('TOTP verification window cannot be negative.');
        for ($offset = -$window; $offset <= $window; $offset++) {
            if (hash_equals(self::code($secret, time() + $offset * 30), $code)) return true;
        }
        return false;
    }

    public static function enable(int $userId, string $secret): void
    {
        $model = ModelRegistry::get('user_totp');
        $row = $model::query()->where('user_id', '=', $userId)->first();
        $data = ['secret' => $secret, 'enabled_at' => date('Y-m-d H:i:s'), 'disabled_at' => null];
        if ($row) {
            $row->update($data);
            return;
        }
        $model::create(['user_id' => $userId] + $data + ['created_at' => date('Y-m-d H:i:s')]);
    }

    public static function enabled(int $userId): ?object
    {
        $model = ModelRegistry::get('user_totp');
        return $model::query()->where('user_id', '=', $userId)->where('disabled_at', '=', null)->first();
    }

    private static function base32(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split($data) as $character) $bits .= str_pad(decbin(ord($character)), 8, '0', STR_PAD_LEFT);
        $output = '';
        foreach (str_split($bits, 5) as $chunk) $output .= $alphabet[bindec(str_pad($chunk, 5, '0'))];
        return $output;
    }

    private static function decode(string $secret): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = str_replace('=', '', strtoupper($secret));
        $bits = '';
        foreach (str_split($secret) as $character) {
            $position = strpos($alphabet, $character);
            if ($position === false) throw new \InvalidArgumentException('Invalid TOTP secret');
            $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }
        $output = '';
        foreach (str_split($bits, 8) as $chunk) if (strlen($chunk) === 8) $output .= chr(bindec($chunk));
        return $output;
    }
}
