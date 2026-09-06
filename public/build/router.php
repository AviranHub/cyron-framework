<?php
// PHP Development Server Router
// This file routes all requests to index.php except for actual files and directories

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestPath = __DIR__ . $uri;

// List of static file extensions to skip
$staticExtensions = [
    'css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'woff', 'woff2', 'ttf', 'eot',
    'ico', 'json', 'xml', 'txt', 'map', 'pdf', 'html', 'htm', 'mp3', 'mp4', 'avi', 'mov',
];

// Debug: log all requests
error_log("[ROUTER] URI: $uri, Path: $requestPath, Exists: " . (file_exists($requestPath) ? 'YES' : 'NO'));

// If the request is to a file that exists and has a static extension, serve it
if (is_file($requestPath)) {
    $ext = strtolower(pathinfo($requestPath, PATHINFO_EXTENSION));
    if (in_array($ext, $staticExtensions)) {
        error_log("[ROUTER] Serving static file: $requestPath");
        return false; // Let PHP serve it
    }
}

// If it's a directory that exists, let PHP handle it
if (is_dir($requestPath)) {
    error_log("[ROUTER] Is directory: $requestPath");
    return false;
}

// Route all other requests to index.php
error_log("[ROUTER] Routing to index.php");
require __DIR__ . '/index.php';

