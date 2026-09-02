<?php
declare(strict_types=1);
require_once __DIR__.'/../app/database/SqlGuard.php';
use App\Database\SqlGuard;
$bad=['users;DROP TABLE users','id DESC','users--comment'];
foreach($bad as $value){try{SqlGuard::identifier($value);echo "FAIL: accepted $value\n";exit(1);}catch(InvalidArgumentException $e){}}
if(SqlGuard::direction('desc')!=='DESC')exit(1);
try{SqlGuard::operator('OR 1=1');exit(1);}catch(InvalidArgumentException $e){}
echo "PASS: SQL guard regression checks passed\n";
