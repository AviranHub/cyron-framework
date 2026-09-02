<?php
namespace App\Core\Plugin;

abstract class Plugin
{
    /**
     * اطلاعات پلاگین (از plugin.json)
     */
    protected array $info = [];

    public function __construct()
    {
        $this->info = $this->getInfo();
    }

    /**
     * متد boot هنگام فعال شدن پلاگین اجرا می‌شود
     */
    public function boot(): void
    {
        // برای ثبت هوک‌ها
        $this->registerHooks();
    }

    /**
     * متد disable هنگام غیرفعال شدن پلاگین اجرا می‌شود
     */
    public function disable(): void
    {
        // پاکسازی داده‌ها (در صورت نیاز)
    }

    /**
     * ثبت هوک‌های پلاگین
     */
    protected function registerHooks(): void
    {
        // در کلاس فرزند override شود
    }

    /**
     * دریافت اطلاعات پلاگین از فایل plugin.json
     */
    protected function getInfo(): array
    {
        $reflection = new \ReflectionClass($this);
        $dir = dirname($reflection->getFileName());
        $manifest = $dir . '/plugin.json';
        if (file_exists($manifest)) {
            return json_decode(file_get_contents($manifest), true);
        }
        return [];
    }

    /**
     * تابع کمکی برای ثبت هوک
     */
    protected function listen(string $hook, callable $callback): void
    {
        HookManager::listen($hook, $callback);
    }

    /**
     * دریافت مسیر پلاگین
     */
    public function getPath(): string
    {
        return dirname((new \ReflectionClass($this))->getFileName());
    }

    /**
     * دریافت view پلاگین
     */
    public function view(string $view, array $data = []): string
    {
        $path = $this->getPath() . '/Views/' . str_replace('.', '/', $view) . '.lady.php';
        if (file_exists($path)) {
            return view($path, $data);
        }
        return '';
    }
}