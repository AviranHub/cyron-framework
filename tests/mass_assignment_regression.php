<?php
declare(strict_types=1);
require_once __DIR__.'/../app/database/Model.php';

use App\Database\Model;

$failed=0;
function expectSecurity(bool $ok,string $name):void{global $failed;if(!$ok){$failed++;echo "FAIL: $name\n";}else echo "PASS: $name\n";}

class SecurityTestModel extends Model {
    protected static $table='security_test';
    protected static array $fillable=['name','email'];
}

$m=new SecurityTestModel();
$m->fill(['name'=>'Reza','email'=>'reza@example.test','is_admin'=>1,'role'=>'admin']);
expectSecurity($m->name==='Reza','fillable field accepted');
expectSecurity($m->email==='reza@example.test','second fillable field accepted');
expectSecurity($m->is_admin===null,'is_admin blocked by mass assignment');
expectSecurity($m->role===null,'role blocked by mass assignment');

class LockedSecurityTestModel extends Model {
    protected static $table='security_test';
}
$locked=new LockedSecurityTestModel();
$locked->fill(['name'=>'Reza','is_admin'=>1]);
expectSecurity($locked->name===null && $locked->is_admin===null,'default model denies all mass assignment');

exit($failed?1:0);
