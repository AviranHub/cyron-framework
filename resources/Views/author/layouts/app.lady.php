<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>پنل نویسندگی | کلبه کتاب</title>
    <link rel="stylesheet" href="/build/assets/style.css">
    <script type="module" src="/build/assets/script.js"></script>
</head>

<body class="author-app">
    <div x-data="{ sidebar: false }" class="min-h-screen lg:flex">
        <div x-show="sidebar" x-transition.opacity class="fixed inset-0 z-30 bg-slate-950/40 lg:hidden" @click="sidebar = false"></div>
        <aside class="fixed inset-y-0 right-0 z-40 w-72 -translate-x-full bg-emerald-950 px-5 py-6 text-emerald-50 shadow-2xl transition-transform lg:static lg:w-72 lg:translate-x-0 lg:shadow-none" :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <a href="{{ route('author.dashboard') }}" class="flex items-center gap-3 border-b border-white/10 pb-6"><span class="grid h-11 w-11 place-items-center rounded-xl bg-amber-300 text-xl font-black text-emerald-950">ک</span><span><strong class="block">کلبه کتاب</strong><small class="text-xs text-emerald-200/70">پنل نویسندگی</small></span></a>
            <nav class="mt-7 space-y-2"><a href="{{ route('author.dashboard') }}" class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 font-bold text-white"><i class="fas fa-grid-2 w-5 text-center text-amber-200"></i> نمای کلی</a><a href="{{ route('author.books') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-emerald-100 transition hover:bg-white/10"><i class="fas fa-book w-5 text-center text-cyan-200"></i> آثار من</a><a href="{{ route('author.books.create') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-emerald-100 transition hover:bg-white/10"><i class="fas fa-plus w-5 text-center text-amber-200"></i> افزودن اثر</a>
                <p class="px-4 pt-7 text-xs font-bold text-emerald-300/60">به‌زودی</p><span class="flex items-center gap-3 px-4 py-3 text-emerald-200/50"><i class="fas fa-chart-line w-5 text-center"></i> آمار و درآمد</span><span class="flex items-center gap-3 px-4 py-3 text-emerald-200/50"><i class="fas fa-headphones w-5 text-center"></i> استودیو صوتی</span>
            </nav>
            <div class="absolute inset-x-5 bottom-6 border-t border-white/10 pt-5"><a href="{{ route('home') }}" class="mb-3 flex items-center gap-3 px-4 py-2 text-sm text-emerald-100"><i class="fas fa-arrow-left"></i> بازگشت به سایت</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="px-4 py-2 text-sm text-rose-200" type="submit"><i class="fas fa-right-from-bracket ml-2"></i> خروج از حساب</button></form>
            </div>
        </aside>
        <main class="min-w-0 flex-1">
            <header class="sticky top-0 z-20 flex min-h-16 items-center justify-between border-b border-emerald-100 bg-white/90 px-4 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-950/90 sm:px-8"><button class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700 lg:hidden" type="button" @click="sidebar = true" aria-label="باز کردن منو"><i class="fas fa-bars"></i></button>
                <div><span class="block text-xs text-slate-500">فضای نویسندگی</span><strong>{{ $user->name ?? 'نویسنده' }}</strong></div><a href="{{ route('home') }}" class="grid h-10 w-10 place-items-center rounded-xl bg-cyan-50 text-cyan-700" title="صفحه اصلی"><i class="fas fa-house"></i></a>
            </header>
            <div class="mx-auto max-w-7xl p-4 sm:p-8">@yield('content')</div>
        </main>
    </div>
</body>

</html>
<aside class="author-sidebar">
    <a class="author-brand" href="{{ route('home') }}"><span>ک</span>
        <div><strong>کلبه کتاب</strong><small>پنل نویسندگی</small></div>
    </a>
    <nav class="author-nav"><a class="author-link active" href="{{ route('author.dashboard') }}"><i class="fas fa-grid-2"></i> نمای کلی</a><a class="author-link" href="{{ route('author.books') }}"><i class="fas fa-book"></i> آثار من</a><a class="author-link author-disabled" href="#" aria-disabled="true"><i class="fas fa-headphones"></i> استودیو صوتی <em>به‌زودی</em></a><a class="author-link author-disabled" href="#" aria-disabled="true"><i class="fas fa-chart-line"></i> آمار و درآمد <em>به‌زودی</em></a>
        <div class="author-label">حساب</div><a class="author-link" href="{{ route('user.profile.edit') }}"><i class="fas fa-user"></i> پروفایل من</a><a class="author-link" href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> بازگشت به سایت</a>
    </nav>
    <form class="author-logout" method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><i class="fas fa-right-from-bracket"></i> خروج از حساب</button></form>
</aside>
<main class="author-main">
    <header class="author-topbar"><button class="author-menu" type="button" aria-label="منو"><i class="fas fa-bars"></i></button>
        <div><span>فضای نویسندگی</span><strong>{{ $user->name ?? 'نویسنده' }}</strong></div><a href="{{ route('home') }}" title="صفحه اصلی"><i class="fas fa-house"></i></a>
    </header>
    <div class="author-content">@yield('content')</div>
</main>
</div>
<script>
    document.querySelector('.author-menu')?.addEventListener('click', () => document.querySelector('.author-sidebar')?.classList.toggle('open'));
</script>
</body>

</html>