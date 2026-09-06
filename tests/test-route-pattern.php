<?php
// Test route matching logic directly

$route = '/api/v1/books/{slug}';

$pattern = preg_replace('/\{([a-zA-Z0-9_]+)(\?)?\}/', '(?P<$1>[^/]+)$2', $route);
$pattern = '#^' . $pattern . '$#';

$testUris = [
    '/api/v1/books/c03f39',
    '/api/v1/books/خواندنی-کتاب-c03f39',
    '/api/v1/books/test-slug',
    '/api/v1/books/123',
];

echo "Pattern: " . $pattern . "\n\n";

foreach ($testUris as $uri) {
    $matches = [];
    $result = preg_match($pattern, $uri, $matches);
    echo "URI: $uri\n";
    echo "Match Result: " . ($result ? "✓ YES" : "✗ NO") . "\n";
    if ($result) {
        echo "Params: " . json_encode(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY)) . "\n";
    }
    echo "\n";
}
