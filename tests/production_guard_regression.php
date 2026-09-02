<?php
declare(strict_types=1);
require_once __DIR__.'/../app/Core/Http/Security/ProductionGuard.php';
use App\Core\Http\Security\ProductionGuard;
putenv('APP_ENV=production');putenv('APP_DEBUG=false');putenv('APP_URL=https://example.test');putenv('APP_KEY=123456789');
$failed=false;try{ProductionGuard::validate();}catch(RuntimeException $e){$failed=true;}echo $failed?"PASS: weak APP_KEY rejected\n":"FAIL: weak APP_KEY accepted\n";exit($failed?0:1);
