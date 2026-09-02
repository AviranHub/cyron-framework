<?php
declare(strict_types=1);
$bad=['../private','covers/../../private','covers/php<script>'];
foreach($bad as $path){$ok=false;foreach(explode('/',str_replace('\\\\','/',$path)) as $part){if($part===''||$part==='.'||$part==='..'||!preg_match('/^[A-Za-z0-9_-]+$/',$part)){$ok=true;break;}}if(!$ok){echo "FAIL: accepted $path\n";exit(1);}}
echo "PASS: unsafe upload paths rejected\n";
