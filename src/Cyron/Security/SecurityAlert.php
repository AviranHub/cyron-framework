<?php

namespace Cyron\Security;

class SecurityAlert
{
    public static function buildMessage(string $event, array $context = []): string
    {
        $lines = [
            'Security event: ' . $event,
            'Time: ' . date('c'),
            'Environment: ' . (getenv('APP_ENV') ?: 'production'),
        ];

        foreach ($context as $key => $value) {
            if (preg_match('/password|secret|token|authorization/i', (string) $key)) {
                $value = '[REDACTED]';
            }
            $lines[] = $key . ': ' . (is_scalar($value) || $value === null
                ? (string) $value
                : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR));
        }

        return implode("\n", $lines);
    }
}
