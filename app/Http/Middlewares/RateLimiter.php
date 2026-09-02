<?php
// app/Http/Middlewares/RateLimiter.php

namespace App\Http\Middlewares;

use App\Http\Middleware;
use App\Core\Cache\CacheManager;

class RateLimiter extends Middleware
{
    private const PROFILES = ['login' => [5, 1], 'register' => [5, 1], 'password_reset' => [3, 1], 'api' => [60, 1]];

    public function __construct(?string $profile = null)
    {
        if ($profile !== null) {
            if (!isset(self::PROFILES[$profile])) throw new \InvalidArgumentException('Unknown rate limit profile.');
            [$this->maxAttempts, $this->decayMinutes] = self::PROFILES[$profile];
        }
    }
    protected int $maxAttempts = 60;
    protected int $decayMinutes = 1;

    public function handle($request, $next)
    {
        $key = $this->getKey($request);
        $attempts = CacheManager::increment($key, 1, $this->decayMinutes * 60);
        $remaining = max(0, $this->maxAttempts - $attempts);
        header('X-RateLimit-Limit: ' . $this->maxAttempts);
        header('X-RateLimit-Remaining: ' . $remaining);

        if ($attempts > $this->maxAttempts) {
            header('Retry-After: ' . ($this->decayMinutes * 60));
            header('X-RateLimit-Profile: active');
            return response()->error('تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً بعداً تلاش کنید.', 429);
        }
        return $next($request);
    }

    protected function getKey($request): string
    {
        if (isset($request->user) && $request->user) {
            return 'rate_limit_user_' . (int)$request->user->id;
        }
        return 'rate_limit_ip_' . hash('sha256', (string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'));
    }

    public function setMaxAttempts(int $maxAttempts): self
    {
        if ($maxAttempts < 1) throw new \InvalidArgumentException('maxAttempts must be positive.');
        $this->maxAttempts = $maxAttempts;
        return $this;
    }

    public function setDecayMinutes(int $decayMinutes): self
    {
        if ($decayMinutes < 1) throw new \InvalidArgumentException('decayMinutes must be positive.');
        $this->decayMinutes = $decayMinutes;
        return $this;
    }
}