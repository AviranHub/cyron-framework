<?php
// app/Config/mail.php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Mail Driver
    |--------------------------------------------------------------------------
    | پشتیبانی از: 'smtp', 'sendmail', 'mail'
    */
    'default' => env('MAIL_DRIVER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Server Configuration
    |--------------------------------------------------------------------------
    */
    'smtp' => [
        'host' => env('MAIL_HOST', 'smtp.gmail.com'),
        'port' => env('MAIL_PORT', 587),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'timeout' => 30,
        'auth_mode' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Sendmail Configuration
    |--------------------------------------------------------------------------
    */
    'sendmail' => [
        'path' => '/usr/sbin/sendmail -bs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Cyron Framework'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Settings (برای ایمیل‌های زیبا)
    |--------------------------------------------------------------------------
    */
    'markdown' => [
        'theme' => 'default',
        'paths' => [RESOURCES_PATH . '/views/mail'],
    ],
];