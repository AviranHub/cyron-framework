<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@title('Cyron Admin')</title>
<link rel="stylesheet" href="{{ asset('modules/admin/css/admin-modern.css') }}">
</head>
<body>
<div class="admin-shell">
<aside class="sidebar">
<div class="brand">✦ <span>Cyron</span><small>ADMIN</small></div>
<nav class="nav">
<a class="nav-item active" href="{{ route('admin.dashboard') }}">⌂ داشبورد</a>
@foreach(($adminModules ?? []) as $key => $module)
<a class="nav-item" href="{{ route('admin.' . $key . '.index') }}">{{ $module['label'] ?? ucfirst($key) }}</a>
@endforeach
</nav>
<div class="sidebar-footer"><a href="{{ route('logout') }}">خروج</a></div>
</aside>
<section class="main">
<header class="topbar"><button class="menu-toggle" aria-label="منو">☰</button><div class="page-context">@yield('page_title','مدیریت')</div><div class="admin-user">مدیر سیستم</div></header>
<main class="content">@yield('content')</main>
</section></div>
<script>document.querySelector('.menu-toggle')?.addEventListener('click',()=>document.querySelector('.sidebar').classList.toggle('open'));</script>
</body></html>