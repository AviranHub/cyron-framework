<?php

namespace Cyron\Console\Commands;

use Cyron\Console\Colors;

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
        $generator = new \Cyron\Sitemap\SitemapGenerator();
        $path = $generator->generate();
        echo Colors::green("✓ Sitemap generated successfully at: {$path}\n");
    }
}