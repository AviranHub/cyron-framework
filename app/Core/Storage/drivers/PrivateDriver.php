<?php
namespace App\Core\Storage\drivers;

class PrivateDriver extends LocalDriver
{
    public function __construct(string $root)
    {
        parent::__construct($root, 'private');
    }

    public function url(string $path): string
    {
        throw new \RuntimeException("Private driver does not support direct URL access.");
    }
}