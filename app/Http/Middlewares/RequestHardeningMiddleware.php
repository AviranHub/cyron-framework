<?php
namespace App\Http\Middlewares;
use App\Http\Middleware;
class RequestHardeningMiddleware extends Middleware
{
    private const DISALLOWED_METHODS=['TRACE','TRACK','CONNECT'];
    private const DEFAULT_MAX_BODY_SIZE=10485760;
    public function handle($request,$next)
    {
        $method=strtoupper((string)($_SERVER['REQUEST_METHOD']??'GET'));
        if(in_array($method,self::DISALLOWED_METHODS,true)){http_response_code(405);return 'Method Not Allowed';}
        $host=(string)($_SERVER['HTTP_HOST']??$_SERVER['SERVER_NAME']??'');
        if($host!==''&&!preg_match('/^[A-Za-z0-9.-]+(?::\\d+)?$/',$host)){http_response_code(400);return 'Invalid Host header';}
        $length=$_SERVER['CONTENT_LENGTH']??null;
        if($length!==null&&$length!==''&&!ctype_digit((string)$length)){http_response_code(400);return 'Invalid Content-Length header';}
        $max=(int)(getenv('APP_MAX_BODY_SIZE')?:self::DEFAULT_MAX_BODY_SIZE);
        if($max>0&&is_numeric($length)&&(int)$length>$max){http_response_code(413);return 'Request payload too large';}
        return $next($request);
    }
}
