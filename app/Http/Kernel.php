<?php

namespace App\Http;

class Kernel
{
    protected $middleware = [];

    public function registerMiddleware($middleware)
    {
        $this->middleware[] = $middleware;
    }

    public function handle()
    {
        $handler = $this->createMiddlewareChain();
        return $handler->handle();
    }

    protected function createMiddlewareChain()
    {
        $next = null;

        // ایجاد زنجیره‌ای از میدلورها
        foreach (array_reverse($this->middleware) as $middleware) {
            $next = new $middleware($next);
        }

        return $next;
    }
}

