<?php
declare(strict_types=1);
foreach (glob(__DIR__.'/../app/Models/*.php') as $file) {
    $code=file_get_contents($file);
    if (preg_match('/(?:protected|public|private)\\s+(?:static\\s+)?\\$fillable\\b/', $code)) {
        echo "FAIL: untyped fillable in $file\n"; exit(1);
    }
    if (preg_match('/(?:protected|public|private)\\s+(?:static\\s+)?\\$guarded\\b/', $code)) {
        echo "FAIL: untyped guarded in $file\n"; exit(1);
    }
}
echo "PASS: model fillable and guarded overrides are type-compatible\n";
