<?php
namespace App\Auth;
use App\Models\UserTotp;
class Totp {
 public static function generateSecret(int $bytes=20): string { return self::base32(random_bytes($bytes)); }
 public static function provisioningUri(string $issuer,string $account,string $secret): string {
  return 'otpauth://totp/'.rawurlencode($issuer.':'.$account).'?secret='.rawurlencode($secret).'&issuer='.rawurlencode($issuer).'&algorithm=SHA1&digits=6&period=30';
 }
 public static function code(string $secret,?int $time=null): string {
  $counter=intdiv($time ?? time(),30);$key=self::decode($secret);$bin=pack('N*',0,$counter);$hash=hash_hmac('sha1',$bin,$key,true);$offset=ord($hash[19])&15;$num=((ord($hash[$offset])&127)<<24)|((ord($hash[$offset+1])&255)<<16)|((ord($hash[$offset+2])&255)<<8)|(ord($hash[$offset+3])&255);return str_pad((string)($num%1000000),6,'0',STR_PAD_LEFT);
 }
 public static function verify(string $secret,string $code,int $window=1): bool { for($i=-$window;$i<=$window;$i++)if(hash_equals(self::code($secret,time()+$i*30),$code))return true;return false; }
 public static function enable(int $userId,string $secret): void { $row=UserTotp::query()->where('user_id','=',$userId)->first();$data=['secret'=>$secret,'enabled_at'=>date('Y-m-d H:i:s'),'disabled_at'=>null];if($row)$row->update($data);else UserTotp::create(['user_id'=>$userId]+$data+['created_at'=>date('Y-m-d H:i:s')]); }
 public static function enabled(int $userId): ?UserTotp { return UserTotp::query()->where('user_id','=',$userId)->where('disabled_at','=',null)->first(); }
 private static function base32(string $data): string {$a='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';$bits='';foreach(str_split($data) as $c)$bits.=str_pad(decbin(ord($c)),8,'0',STR_PAD_LEFT);$out='';foreach(str_split($bits,5) as $chunk){$chunk=str_pad($chunk,5,'0');$out.=$a[bindec($chunk)];}return $out;}
 private static function decode(string $s): string {$a='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';$s=str_replace('=','',strtoupper($s));$bits='';foreach(str_split($s) as $c){$p=strpos($a,$c);if($p===false)throw new \InvalidArgumentException('Invalid TOTP secret');$bits.=str_pad(decbin($p),5,'0',STR_PAD_LEFT);} $out='';foreach(str_split($bits,8) as $chunk)if(strlen($chunk)===8)$out.=chr(bindec($chunk));return $out;}
}