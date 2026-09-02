<?php
use App\Analytics\MetricRegistry;

MetricRegistry::registerMany([
    'daily_user_registrations' => ['label'=>'ثبت‌نام‌های کاربر','event'=>'user.registered','aggregation'=>'count'],
    // Example: ['label'=>'درآمد','event'=>'order.completed','aggregation'=>'sum','property'=>'total'],
    // Applications can define their own KPIs here.
]);