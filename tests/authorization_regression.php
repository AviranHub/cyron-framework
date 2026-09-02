<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/Core/Authorization/Gate.php';
use App\Core\Authorization\Gate;
$failed=0;
function expectAuth(bool $ok,string $name):void{global $failed;if(!$ok){$failed++;echo "FAIL: $name\n";}else echo "PASS: $name\n";}
class AuthzTestUser { public function hasPermission($ability){return in_array($ability,['users.view','books.view'],true);} }
$user=new AuthzTestUser();
expectAuth(Gate::allowsAny(['users.delete','users.view'],$user),'allowsAny grants when one matches');
expectAuth(!Gate::allowsAll(['users.view','users.delete'],$user),'allowsAll denies when one missing');
expectAuth(Gate::allowsAll(['users.view','books.view'],$user),'allowsAll grants when all match');
exit($failed?1:0);
