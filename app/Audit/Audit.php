<?php
namespace App\Audit;
use App\Models\AuditLog;
class Audit {
 public static function record(string $action,array $context=[],?int $actorId=null):void{
  $request=function_exists('request')?request():null;
  $actorId ??= $request && method_exists($request,'user') && $request->user() ? $request->user()->id : null;
  $context=array_merge([
   'request'=>[
    'route'=>$request && method_exists($request,'path')?$request->path():null,
    'method'=>$request && method_exists($request,'method')?$request->method():null,
    'ip'=>$request && method_exists($request,'ip')?$request->ip():null,
    'user_agent'=>$request && method_exists($request,'userAgent')?$request->userAgent():null,
   ]
  ],$context);
  AuditLog::create(['actor_id'=>$actorId,'action'=>$action,'context'=>json_encode($context,JSON_UNESCAPED_UNICODE),'occurred_at'=>date('Y-m-d H:i:s')]);
 }
}