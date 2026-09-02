<?php
namespace App\Http\Middlewares;
use App\Http\Middleware;
use App\Core\Authentication\Auth;
class SessionTimeoutMiddleware extends Middleware
{
    private const DEFAULT_TIMEOUT=1800;
    public function handle($request,$next)
    {
        if(session_status()!==PHP_SESSION_ACTIVE)session_start();
        $now=time();$last=$_SESSION['last_activity']??null;
        $timeout=(int)(getenv('APP_SESSION_IDLE_TIMEOUT')?:self::DEFAULT_TIMEOUT);
        if($timeout>0&&is_numeric($last)&&$now-(int)$last>$timeout){Auth::logout();http_response_code(401);return 'Session expired';}
        $_SESSION['last_activity']=$now;
        return $next($request);
    }
}
