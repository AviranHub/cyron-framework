<?php
require_once __DIR__ . '/../Colors.php';

class SitemapGenerateCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Generate sitemap.xml from application routes";
    }

    public function execute()
    {
        require_once BASE_PATH . '/app/Core/Sitemap/SitemapGenerator.php';
        $generator = new \App\Core\Sitemap\SitemapGenerator();
        $path = $generator->generate();
        echo Colors::green("✓ Sitemap generated successfully at: {$path}\n");
    }
}