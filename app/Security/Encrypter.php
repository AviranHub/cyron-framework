<?php
namespace App\Security;
class Encrypter {
 private static function key(): string { $key=getenv('CYRON_ENCRYPTION_KEY') ?: ''; if(strlen($key)<32)throw new \RuntimeException('CYRON_ENCRYPTION_KEY must contain at least 32 characters.');return hash('sha256',$key,true); }
 public static function encrypt(string $value): string {$iv=random_bytes(12);$tag='';$cipher=openssl_encrypt($value,'aes-256-gcm',self::key(),OPENSSL_RAW_DATA,$iv,$tag);if($cipher===false)throw new \RuntimeException('Encryption failed.');return base64_encode($iv.$tag.$cipher);}
 public static function decrypt(string $payload): string {$data=base64_decode($payload,true);if($data===false||strlen($data)<29)throw new \InvalidArgumentException('Invalid encrypted payload.');$iv=substr($data,0,12);$tag=substr($data,12,16);$cipher=substr($data,28);$plain=openssl_decrypt($cipher,'aes-256-gcm',self::key(),OPENSSL_RAW_DATA,$iv,$tag);if($plain===false)throw new \RuntimeException('Decryption failed.');return $plain;}
}