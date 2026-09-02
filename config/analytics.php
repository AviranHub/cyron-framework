<?php
use App\Analytics\EventRegistry;

EventRegistry::registerMany([
    'user.registered' => ['label' => 'ثبت‌نام کاربر', 'category' => 'users'],
    'user.logged_in' => ['label' => 'ورود کاربر', 'category' => 'users'],
    // Applications can add any domain-specific events here.
]);