#!/usr/bin/env php
<?php

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('RESOURCES_PATH', BASE_PATH . '/resources');
define('ROUTES_PATH', BASE_PATH . '/routes');
define('STORAGE_PATH', BASE_PATH . '/storage');

// Composer is the primary autoloader; keep the legacy loader as a fallback.
$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
}
require_once APP_PATH . '/Core/Env.php';
\App\Core\Env::load(BASE_PATH . '/.env');
require_once APP_PATH . '/Libs/jdf.php';
// اصلاح مسیرها برای پروژه شما - بدون نیاز به vendor/autoload.php
// The legacy autoloader recursively loads application models, so their base class
// must exist before autoload.php is included.
require_once __DIR__ . '/app/database/Model.php';
require_once __DIR__ . '/app/autoload.php';
require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/str.php';
require_once __DIR__ . '/app/router.php';
// require_once __DIR__ . '/app/view.php';
require_once __DIR__ . '/app/lady.php';
require_once __DIR__ . '/app/Request.php';

// Load database classes
require_once __DIR__ . '/app/database/Db.php';
require_once __DIR__ . '/app/database/Migration.php';

// Load models
// require_once __DIR__ . '/app/Models/User.php';
// require_once __DIR__ . '/app/Models/Guild.php';
// require_once __DIR__ . '/app/Models/GuildCategory.php';
// require_once __DIR__ . '/app/Models/Slider.php';

// Load controller classes
require_once __DIR__ . '/app/Http/Controller.php';
require_once __DIR__ . '/app/Http/ErorrBag.php';
require_once __DIR__ . '/app/Http/Storage.php';
require_once __DIR__ . '/app/Http/Middleware.php';

// Load all command classes
if (!file_exists(__DIR__ . '/cli/Console.php')) {
    mkdir(__DIR__ . '/cli', 0777, true);
}

require_once __DIR__ . '/cli/Console.php';
require_once __DIR__ . '/cli/Input.php';

if (!file_exists(__DIR__ . '/cli/Commands')) {
    mkdir(__DIR__ . '/cli/Commands', 0777, true);
}

foreach (glob(__DIR__ . '/cli/Commands/*.php') as $file) {
    require_once $file;
}

// Run console
$console = new Console();
$console->run();