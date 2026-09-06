<?php

namespace Cyron\Http;

class Controller
{
    protected $middleware = [];

    public function middleware($middleware, $options = [])
    {
        if (is_array($middleware)) {
            foreach ($middleware as $item) $this->middleware[] = $item;
        } else {
            $this->middleware[] = $middleware;
        }
    }

    public function handleMiddleware()
    {
        foreach ($this->middleware as $middleware) {
            $middlewareInstance = new $middleware();
            $response = $middlewareInstance->handle();
            if ($response !== null) return $response;
        }
        return null;
    }
}
