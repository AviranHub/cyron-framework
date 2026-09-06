<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$autoload = $root . '/vendor/autoload.php';
if (!is_file($autoload)) {
    echo "FAIL: Composer autoload is missing\n";
    exit(1);
}

require $autoload;

$classes = [
    'Cyron\\Support\\Env',
    'Cyron\\Support\\Str',
    'Cyron\\Database\\Db',
    'Cyron\\Database\\SqlGuard',
    'Cyron\\Database\\Collection',
    'Cyron\\Database\\Paginator',
    'Cyron\\Database\\Migration',
    'Cyron\\Database\\TableBuilder',
    'Cyron\\Database\\Builder',
    'Cyron\\Database\\Relation',
    'Cyron\\Database\\Relations\\BelongsTo',
    'Cyron\\Database\\Relations\\BelongsToMany',
    'Cyron\\Database\\Relations\\HasMany',
    'Cyron\\Database\\Relations\\HasOne',
    'Cyron\\Database\\Relations\\MorphMany',
    'Cyron\\Database\\Relations\\MorphTo',
    'Cyron\\Database\\Relations\\MorphToMany',
    'Cyron\\Http\\Response',
    'Cyron\\Http\\Request',
    'Cyron\\Http\\File',
    'Cyron\\Http\\Middleware',
    'Cyron\\Http\\Controller',
    'Cyron\Routing\Route',
    'Cyron\Lady\Parser',
    'Cyron\Lady\Compiler',
    'Cyron\Lady\Engine',
    'Cyron\Lady\ComponentManager',
    'Cyron\Localization\Translator',
    'Cyron\Exceptions\Handler',
    'Cyron\Plugin\HookManager',
];

foreach ($classes as $class) {
    if (!class_exists($class)) {
        echo "FAIL: Composer could not load {$class}\n";
        exit(1);
    }
}

echo "PASS: Composer loads all extracted Cyron foundation classes\n";
