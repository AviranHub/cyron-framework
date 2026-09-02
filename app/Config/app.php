<?php
return [
    'name' => vars('APP_NAME', 'Cyron'),
    'env' => vars('APP_ENV', 'production'),
    'debug' => vars('APP_DEBUG', false),
    'admin_email' => vars('ADMIN_EMAIL'),
    'admin_password' => vars('ADMIN_PASSWORD'),
    'storage_max_upload_size' => (int)vars('STORAGE_MAX_UPLOAD_SIZE', 10),
];