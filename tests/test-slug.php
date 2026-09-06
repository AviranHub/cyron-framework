<?php
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('ROUTES_PATH', BASE_PATH . '/routes');

$root = BASE_PATH;
require dirname($root) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require APP_PATH . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Models\Book;

$books = Book::all();
echo "First 5 books:\n";
foreach ($books->take(5) as $book) {
    echo "ID: {$book->id}, Slug: {$book->slug}\n";
}

// Test slug matching
$testSlug = 'خواندنی-کتاب-c03f39';
$found = Book::where('slug', '=', $testSlug)->first();
echo "\nSearching for: $testSlug\n";
echo "Found: " . ($found ? "Yes - " . $found->title : "No") . "\n";
