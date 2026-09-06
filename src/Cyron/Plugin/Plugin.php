<?php

namespace Cyron\Plugin;

abstract class Plugin
{
    protected array $info = [];

    public function __construct()
    {
        $this->info = $this->getInfo();
    }

    public function boot(): void
    {
        $this->registerHooks();
    }

    public function disable(): void
    {
        // optional cleanup
    }

    protected function registerHooks(): void
    {
        // override in child class
    }

    protected function getInfo(): array
    {
        $reflection = new \ReflectionClass($this);
        $dir = dirname($reflection->getFileName());
        $manifest = $dir . '/plugin.json';
        if (file_exists($manifest)) {
            return json_decode(file_get_contents($manifest), true) ?: [];
        }
        return [];
    }

    protected function listen(string $hook, callable $callback): void
    {
        HookManager::listen($hook, $callback);
    }

    public function getPath(): string
    {
        return dirname((new \ReflectionClass($this))->getFileName());
    }

    public function view(string $view, array $data = []): string
    {
        $path = $this->getPath() . '/Views/' . str_replace('.', '/', $view) . '.lady.php';
        if (file_exists($path)) {
            return view($path, $data);
        }
        return '';
    }
}
