<?php
namespace Cyron\Authentication;

use Cyron\Database\ModelRegistry;
use Cyron\Security\Encrypter;

class TotpCredential {
 public static function enable(int $userId,string $secret): void { $model=ModelRegistry::get('user_totp'); $encrypted=Encrypter::encrypt($secret);$row=$model::query()->where('user_id','=',$userId)->first();$data=['secret'=>$encrypted,'enabled_at'=>date('Y-m-d H:i:s'),'disabled_at'=>null];if($row)$row->update($data);else $model::create(['user_id'=>$userId]+$data+['created_at'=>date('Y-m-d H:i:s')]);}
 public static function secret(object $totp): string { return Encrypter::decrypt($totp->secret); }
}