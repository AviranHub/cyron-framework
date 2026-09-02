<?php
namespace App\Core\Sitemap;

use App\Route;
use DOMDocument;

class SitemapGenerator
{
    protected array $routes;
    protected array $excludePatterns = [
        '/^\/admin/',
        '/^\/api/',
        '/^\/_debugbar/',
        '/^\/login/',
        '/^\/logout/',
        '/^\/register/',
        '/^\/password/',
        '/^\/phone/',
        '/^\/dashboard/',
        '/^\/profile/',
        '/^\/livewire/',
        '/^\/404/',
        '/^\/_ignition/',
        '/^\/_assets/',
        '/^\/storage/',
    ];

    protected array $priorityMap = [
        '/' => 1.0,
        '/books' => 0.9,
        '/book' => 0.8,
        '/blog' => 0.8,
        '/article' => 0.7,
        '/about-us' => 0.6,
        '/contact-us' => 0.6,
    ];

    protected array $changefreqMap = [
        '/' => 'daily',
        '/books' => 'daily',
        '/book' => 'weekly',
        '/blog' => 'daily',
    ];

    public function __construct()
    {
        // بارگذاری روت‌ها اگر قبلاً انجام نشده باشد
        $this->loadRoutesIfNeeded();
        $this->routes = Route::getRoutes();
    }

    /**
     * بارگذاری فایل‌های روت (در CLI معمولاً بارگذاری نشده‌اند)
     */
    protected function loadRoutesIfNeeded(): void
    {
        // اگر روت‌ها قبلاً تعریف شده‌اند، کاری نکن
        if (!empty(Route::getRoutes())) {
            return;
        }

        // بارگذاری تمام فایل‌های روت در پوشه routes/
        $routeFiles = glob(ROUTES_PATH . '/*.php');
        foreach ($routeFiles as $file) {
            require_once $file;
        }
    }

    /**
     * تولید فایل sitemap.xml در public
     * @return string
     */
    public function generate(): string
    {
        $xml = $this->buildXml();
        $path = BASE_PATH . '/public/sitemap.xml';
        file_put_contents($path, $xml);
        return $path;
    }

    /**
     * ساخت محتوای XML
     */
    protected function buildXml(): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $dom->appendChild($urlset);

        foreach ($this->routes as $route) {
            // فقط متد GET و روت‌های بدون پارامتر
            if ($route['method'] !== 'GET' && $route['method'] !== 'ANY') {
                continue;
            }

            $uri = $route['uri'];
            // حذف روت‌های دارای پارامتر (مثل /book/{slug})
            if (strpos($uri, '{') !== false) {
                continue;
            }
            // حذف بر اساس الگوهای exclude
            if ($this->shouldExclude($uri)) {
                continue;
            }

            $url = $dom->createElement('url');
            $urlset->appendChild($url);

            // loc
            $loc = $dom->createElement('loc', $this->fullUrl($uri));
            $url->appendChild($loc);

            // lastmod – می‌توان از تاریخ امروز استفاده کرد
            $lastmod = $dom->createElement('lastmod', date('Y-m-d'));
            $url->appendChild($lastmod);

            // changefreq
            $changefreq = $this->getChangefreq($uri);
            $freqNode = $dom->createElement('changefreq', $changefreq);
            $url->appendChild($freqNode);

            // priority
            $priority = $this->getPriority($uri);
            $priNode = $dom->createElement('priority', number_format($priority, 1));
            $url->appendChild($priNode);
        }

        return $dom->saveXML();
    }

    /**
     * بررسی اینکه آیا روت باید از sitemap حذف شود
     */
    protected function shouldExclude(string $uri): bool
    {
        foreach ($this->excludePatterns as $pattern) {
            if (preg_match($pattern, $uri)) {
                return true;
            }
        }
        return false;
    }

    /**
     * اولویت روت
     */
    protected function getPriority(string $uri): float
    {
        foreach ($this->priorityMap as $pattern => $priority) {
            if (str_starts_with($uri, $pattern)) {
                return $priority;
            }
        }
        return 0.5;
    }

    /**
     * فرکانس تغییر
     */
    protected function getChangefreq(string $uri): string
    {
        foreach ($this->changefreqMap as $pattern => $freq) {
            if (str_starts_with($uri, $pattern)) {
                return $freq;
            }
        }
        return 'monthly';
    }

    /**
     * ساخت آدرس کامل (دینامیک بر اساس دامنه فعلی)
     */
    protected function fullUrl(string $uri): string
    {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . rtrim($uri, '/');
    }
}