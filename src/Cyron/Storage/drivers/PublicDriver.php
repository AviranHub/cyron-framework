<?php

namespace Cyron\Storage\drivers;

class PublicDriver extends LocalDriver
{
    public function __construct(string $root)
    {
        parent::__construct($root, 'public');
    }
}
