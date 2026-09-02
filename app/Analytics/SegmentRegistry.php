<?php
namespace App\Analytics;
class SegmentRegistry {
 protected static array $segments=[];
 public static function register(string $key,array $definition):void{static::$segments[$key]=array_merge(['label'=>$key,'resolver'=>null],$definition);}
 public static function registerMany(array $segments):void{foreach($segments as $key=>$definition)static::register($key,$definition);}
 public static function all():array{return static::$segments;}
}