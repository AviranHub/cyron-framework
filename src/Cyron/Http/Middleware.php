<?php

namespace Cyron\Http;

abstract class Middleware
{
    abstract public function handle($request, $next);
}
