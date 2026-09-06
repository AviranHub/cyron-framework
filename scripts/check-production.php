<?php

declare(strict_types=1);

$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value);
        if ($key === '') {
            continue;
        }

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        $_ENV[$key] = $value;
        putenv($key . '=' . $value);
    }
}

$checks = [
    'APP_ENV' => static function (string $value): bool {
        return strtolower($value) === 'production';
    },
    'APP_DEBUG' => static function (string $value): bool {
        return strtolower($value) === 'false' || $value === '0';
    },
    'APP_KEY' => static function (string $value): bool {
        $weak = ['','changeme','replace-me','secret','password','123456789'];
        return strlen($value) >= 32 && !in_array(strtolower($value), $weak, true);
    },
    'APP_URL' => static function (string $value): bool {
        return $value !== ''
            && !preg_match('~^(?:https?://)?(?:localhost|127\.0\.0\.1|0\.0\.0\.0|\[::1\])(?:[:/]|$)~i', $value);
    },
];

$failures = [];
foreach ($checks as $key => $validator) {
    $value = getenv($key) ?: ($_ENV[$key] ?? '');
    $ok = is_string($value) && $validator($value);
    if (!$ok) {
        $failures[] = $key;
        echo "FAIL: {$key} is not configured for production.\n";
    }
}

if ($failures !== []) {
    exit(1);
}

echo "PASS: production configuration checks passed.\n";
exit(0);
