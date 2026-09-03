<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حساب کاربری | کلبه کتاب</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('user-dashboard.css?v=1') }}" rel="stylesheet">
</head>
<body class="user-app">
    <div class="user-shell">
        <aside class="user-sidebar">
            <a class="user-brand" href="{{ route('home') }}"><span>ک</span><div><strong>کلبه کتاب</strong><small>حساب کاربری</small></div></a>
            <nav class="user-nav">
                <a class="user-nav-link active" href="{{ route('user.dashboard') }}"><i class="fas fa-house"></i> نمای کلی</a>
                <a class="user-nav-link" href="{{ route('user.profile.edit') }}"><i class="fas fa-user"></i> پروفایل من</a>
                <a class="user-nav-link" href="{{ route('user.change-password') }}"><i class="fas fa-lock"></i> امنیت حساب</a>
                <div class="user-nav-label">دسترسی سریع</div>
                <a class="user-nav-link" href="{{ route('home') }}"><i class="fas fa-book-open"></i> بازگشت به کتاب‌ها</a>
            </nav>
            <form class="user-logout" method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><i class="fas fa-right-from-bracket"></i> خروج از حساب</button></form>
        </aside>
        <main class="user-main">
            <header class="user-topbar"><button class="user-menu" type="button" aria-label="منو"><i class="fas fa-bars"></i></button><div><span>حساب کاربری</span><strong>{{ $user->name ?? 'کاربر' }}</strong></div><a class="user-home" href="{{ route('home') }}" title="صفحه اصلی"><i class="fas fa-arrow-left"></i></a></header>
            <div class="user-content">@yield('content')</div>
        </main>
    </div>
    <script>document.querySelector('.user-menu')?.addEventListener('click',()=>document.querySelector('.user-sidebar')?.classList.toggle('open'));</script>
</body>
</html>