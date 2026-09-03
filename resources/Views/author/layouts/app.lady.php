<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>پنل نویسندگی | کلبه کتاب</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('author-dashboard.css?v=1') }}">
</head>
<body class="author-app">
<div class="author-shell">
    <aside class="author-sidebar">
        <a class="author-brand" href="{{ route('home') }}"><span>ک</span><div><strong>کلبه کتاب</strong><small>پنل نویسندگی</small></div></a>
        <nav class="author-nav"><a class="author-link active" href="{{ route('author.dashboard') }}"><i class="fas fa-grid-2"></i> نمای کلی</a><a class="author-link" href="{{ route('author.books') }}"><i class="fas fa-book"></i> آثار من</a><a class="author-link author-disabled" href="#" aria-disabled="true"><i class="fas fa-headphones"></i> استودیو صوتی <em>به‌زودی</em></a><a class="author-link author-disabled" href="#" aria-disabled="true"><i class="fas fa-chart-line"></i> آمار و درآمد <em>به‌زودی</em></a><div class="author-label">حساب</div><a class="author-link" href="{{ route('user.profile.edit') }}"><i class="fas fa-user"></i> پروفایل من</a><a class="author-link" href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> بازگشت به سایت</a></nav>
        <form class="author-logout" method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><i class="fas fa-right-from-bracket"></i> خروج از حساب</button></form>
    </aside>
    <main class="author-main"><header class="author-topbar"><button class="author-menu" type="button" aria-label="منو"><i class="fas fa-bars"></i></button><div><span>فضای نویسندگی</span><strong>{{ $user->name ?? 'نویسنده' }}</strong></div><a href="{{ route('home') }}" title="صفحه اصلی"><i class="fas fa-house"></i></a></header><div class="author-content">@yield('content')</div></main>
</div>
<script>document.querySelector('.author-menu')?.addEventListener('click',()=>document.querySelector('.author-sidebar')?.classList.toggle('open'));</script>
</body></html>
