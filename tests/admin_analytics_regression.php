<?php

declare(strict_types=1);

$routes = file_get_contents(__DIR__ . '/../routes/admin.php');
$controller = file_get_contents(__DIR__ . '/../app/Http/Controllers/Admin/AnalyticsController.php');
$activityController = file_get_contents(__DIR__ . '/../app/Http/Controllers/Admin/ActivityController.php');
$forumController = file_get_contents(__DIR__ . '/../app/Http/Controllers/Admin/ForumController.php');
$authorRoutes = file_get_contents(__DIR__ . '/../routes/author.php');
$authorController = file_get_contents(__DIR__ . '/../app/Http/Controllers/Author/ContentController.php');
$tracker = file_get_contents(__DIR__ . '/../src/Cyron/Analytics/ActivityTracker.php');
$migration = file_get_contents(__DIR__ . '/../database/Migrations/2026_09_06_000001_add_analytics_indexes_to_user_activities.php');

foreach ([
    "admin.analytics.index",
    "admin.analytics.export",
    "admin.activities.export",
] as $route) {
    if (strpos($routes, $route) === false) {
        echo "FAIL: missing {$route}\n";
        exit(1);
    }
}

if (substr_count($routes, 'AdminMiddleware::class') < 2) {
    echo "FAIL: admin analytics routes are not protected by AdminMiddleware\n";
    exit(1);
}

foreach ([
    'user_activities_occurred_at_index',
    'user_activities_category_occurred_at_index',
    'user_activities_action_occurred_at_index',
] as $index) {
    if (strpos($migration, $index) === false) {
        echo "FAIL: missing analytics index {$index}\n";
        exit(1);
    }
}

foreach (["occurred_at", "category", "amount", "action"] as $column) {
    if (strpos($tracker, "'{$column}'") === false) {
        echo "FAIL: activity tracker no longer writes {$column}\n";
        exit(1);
    }
}

foreach (["MetricRegistry", "dateRange", "csvResponse"] as $feature) {
    if (strpos($controller, $feature) === false) {
        echo "FAIL: analytics controller missing {$feature}\n";
        exit(1);
    }
}

foreach (["recordExport", "admin.report.exported", "-366 days"] as $feature) {
    if (strpos($controller, $feature) === false && strpos($activityController, $feature) === false) {
        echo "FAIL: report export hardening missing {$feature}\n";
        exit(1);
    }
}

foreach ([
    'admin.forum.index',
    'admin.forum.topics',
    'admin.forum.posts',
    'admin.forum.topics.toggle-lock',
    'admin.forum.topics.toggle-pin',
    'admin.forum.topics.destroy',
    'admin.forum.posts.destroy',
] as $route) {
    if (strpos($routes, $route) === false) {
        echo "FAIL: missing {$route}\n";
        exit(1);
    }
}

foreach (['toggleLock', 'togglePin', 'destroyTopic', 'destroyPost', 'admin.forum.topic_deleted'] as $feature) {
    if (strpos($forumController, $feature) === false) {
        echo "FAIL: forum moderation action missing {$feature}\n";
        exit(1);
    }
}

foreach (['author.books.publish', 'author.books.unpublish'] as $route) {
    if (strpos($authorRoutes, $route) === false) {
        echo "FAIL: missing {$route}\n";
        exit(1);
    }
}
foreach (['publishBook', 'unpublishBook', "'status' => 'published'"] as $feature) {
    if (strpos($authorController, $feature) === false) {
        echo "FAIL: author publishing workflow missing {$feature}\n";
        exit(1);
    }
}

echo "PASS: admin analytics routes and activity contract are present\n";