<?php

namespace App\Http;

abstract class Middleware
{
    /**
     * پردازش درخواست و عبور به میدلور بعدی
     * 
     * @param mixed $request
     * @param callable $next
     * @return mixed
     */
    abstract public function handle($request, $next);
}