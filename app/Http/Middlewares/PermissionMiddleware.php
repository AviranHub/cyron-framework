<?php

namespace App\Http\Middlewares;

use App\Http\Middleware;
use App\Core\Authorization\Gate;

class PermissionMiddleware extends Middleware
{
    protected string $ability;

    public function __construct(string $ability = '')
    {
        $this->ability = $ability;
    }

    public function handle($request, $next)
    {
        if ($this->ability === '' || !Gate::allows($this->ability, $request->user ?? null)) {
            http_response_code(403);
            return response()->error('Forbidden.', 403);
        }
        return $next($request);
    }
}