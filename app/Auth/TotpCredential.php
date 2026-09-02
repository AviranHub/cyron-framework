<?php
namespace App\Auth;
use App\Models\UserTotp;use App\Security\Encrypter;
class TotpCredential {
 public static function enable(int $userId,string $secret): void { $encrypted=Encrypter::encrypt($secret);$row=UserTotp::query()->where('user_id','=',$userId)->first();$data=['secret'=>$encrypted,'enabled_at'=>date('Y-m-d H:i:s'),'disabled_at'=>null];if($row)$row->update($data);else UserTotp::create(['user_id'=>$userId]+$data+['created_at'=>date('Y-m-d H:i:s')]);}
 public static function secret(UserTotp $totp): string { return Encrypter::decrypt($totp->secret); }
}