<?php
namespace App\Core\Storage\drivers;

class PublicDriver extends LocalDriver
{
    public function __construct(string $root)
    {
        parent::__construct($root, 'public');
    }
}