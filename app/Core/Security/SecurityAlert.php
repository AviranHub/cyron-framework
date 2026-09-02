<?php
namespace App\Core\Security;
class SecurityAlert {
 public static function buildMessage(string $event,array $context=[]): string {$lines=['Security event: '.$event,'Time: '.date('c'),'Environment: '.(function_exists('env')?(string)env('APP_ENV','production'):'production')];foreach($context as $k=>$v){if(preg_match('/password|secret|token|authorization/i',(string)$k))$v='[REDACTED]';$lines[]=$k.': '.(is_scalar($v)||$v===null?(string)$v:json_encode($v,JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR));}return implode("\n",$lines);}
}