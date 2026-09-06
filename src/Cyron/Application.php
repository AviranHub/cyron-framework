<?php

require_once dirname(__DIR__, 2) . '/src/Cyron/Container/Container.php';

class AppContainer extends \Cyron\Container\Container
{
}

if (!function_exists('app')) {
    function app(?string $abstract = null)
    {
        if ($abstract === null) {
            return new class {
                public function bind(...$args) { return AppContainer::bind(...$args); }
                public function singleton(...$args) { return AppContainer::singleton(...$args); }
                public function make(...$args) { return AppContainer::make(...$args); }
                public function has(...$args) { return AppContainer::has(...$args); }
                public function instance(...$args) { return AppContainer::instance(...$args); }
            };
        }
        return AppContainer::make($abstract);
    }
}
