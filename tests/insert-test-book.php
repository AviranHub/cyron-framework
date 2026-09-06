<?php
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('STORAGE_PATH', BASE_PATH . '/storage');

$root = BASE_PATH;
require $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Cyron' . DIRECTORY_SEPARATOR . 'Support' . DIRECTORY_SEPARATOR . 'Env.php';
\Cyron\Support\Env::load($root . DIRECTORY_SEPARATOR . '.env');
require APP_PATH . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Models\Book;
use Cyron\Database\Db;

// Insert a test book
$db = Db::getInstance();
$stmt = $db->prepare('INSERT INTO books (title, slug, author_name, pages, likes, views, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('sssisss', $title, $slug, $author, $pages, $likes, $views, $status);

$title = 'Test Book';
$slug = 'test-book';
$author = 'Test Author';
$pages = 100;
$likes = 10;
$views = 50;
$status = 'published';

if ($stmt->execute()) {
    echo "Book inserted successfully with slug: $slug\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}

$stmt->close();
$db->close();
