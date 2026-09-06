<?php
use Cyron\Analytics\EventRegistry;

EventRegistry::registerMany([
    'user.registered' => ['label' => 'ثبت‌نام کاربر', 'category' => 'users'],
    'user.logged_in' => ['label' => 'ورود کاربر', 'category' => 'users'],
    'book.viewed' => ['label' => 'بازدید کتاب', 'category' => 'reading'],
    'forum.topic_viewed' => ['label' => 'بازدید موضوع انجمن', 'category' => 'reading'],
    // Applications can add any domain-specific events here.
]);