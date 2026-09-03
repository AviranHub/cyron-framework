<!DOCTYPE html>
<html lang="{{ locale() }}" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>پنل مدیریت | </title>
    <link rel="stylesheet" href="/assets/css/all.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/admin-modern.css">
    <link rel="stylesheet" href="/assets/admin-dashboard.css?v=2">
    <link rel="stylesheet" href="/assets/admin-reference.css?v=1">
    <link rel="stylesheet" href="/assets/admin-polish.css?v=1">
    <link rel="stylesheet" href="/assets/admin-forms.css?v=1">
    <style>
        /* اسکرول اختصاصی برای نوار کناری */
        .sidebar {
            width: 260px;
            transition: transform 0.3s ease-in-out;
            overflow-y: auto;
            scrollbar-width: thin;
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #2d2d2d;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 10px;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                top: 0;
                bottom: 0;
                right: auto;
                left: 0;
                z-index: 50;
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }
        /* آیتم فعال منو */
        .nav-item-active {
            background-color: #4f46e5;
            color: white;
        }
        .nav-item-active i {
            color: white;
        }
        /* ساب منو */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .submenu.open {
            max-height: 300px;
            transition: max-height 0.3s ease-in;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans admin-reference theme-green">

<div class="flex min-h-screen relative">
    <!-- Sidebar -->
    <aside class="sidebar bg-zinc-800 text-white flex flex-col shadow-xl" id="sidebar">
        <!-- لوگو/عنوان -->
        <div class="text-center py-6 border-b border-zinc-700">
            <div class="admin-avatar">م</div>
            <h2 class="text-xl font-bold tracking-wide">مرکز مدیریت</h2>
            <p class="text-xs text-zinc-400 mt-1">کلبه کتاب / فضای ادمین</p>
        </div>

        <!-- منو (پویا از روی کانفیگ) -->
        <nav class="flex-1 px-3 py-4">
            @php
                $adminMenus = config('admin', []);
                // حذف آیتم‌هایی که نباید در منو نمایش داده شوند
                $excludeFromMenu = ['settings'];
            @endphp
            <ul class="space-y-1.5">
                <!-- داشبورد (همیشه هست) -->
                <li>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors duration-200 hover:bg-indigo-700/40 {{ request()->routeIs('admin.dashboard') ? 'nav-item-active' : '' }}">
                        <i class="fas fa-tachometer-alt w-5 text-center"></i>
                        <span>داشبورد</span>
                    </a>
                </li>

                <li class="admin-nav-label">مدیریت محتوا</li>
                @foreach($adminMenus as $key => $menu)
                    @if(in_array($key, $excludeFromMenu)) @continue @endif
                    @if(!isset($menu['model']) || !$menu['model']) @continue @endif
                    
                    <li>
                        <a href="{{ route("admin.{$key}.index") }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors duration-200 hover:bg-indigo-700/40 {{ request()->routeIs("admin.{$key}.*") ? 'nav-item-active' : '' }}">
                            <i class="fas fa-{{ $menu['icon'] ?? 'box' }} w-5 text-center"></i>
                            <span>{{ $menu['label'] }}</span>
                        </a>
                    </li>
                @endforeach

                <li class="admin-nav-label admin-nav-label-spaced">امنیت و سیستم</li>
                <li><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors duration-200 hover:bg-indigo-700/40"><i class="fas fa-chart-line w-5 text-center"></i><span>نمای کلی سیستم</span></a></li>

                <li class="pt-4 mt-2 border-t border-zinc-700">
                    <!-- خروج -->
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors duration-200 hover:bg-red-700/40 text-red-300 hover:text-white">
                            <i class="fas fa-sign-out-alt w-5 text-center"></i>
                            <span>خروج</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- فوتر سایدبار -->
        <div class="p-4 text-center text-xs text-zinc-500 border-t border-zinc-700">
            نسخه 1.0
        </div>
    </aside>

    <!-- Overlay برای موبایل -->
    <div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity" onclick="closeSidebar()"></div>

    <!-- محتوای اصلی -->
    <main class="flex-1 p-4 md:p-6 overflow-x-auto">
        <header class="admin-topbar">
            <div><h1>داشبورد</h1><span>مرکز کنترل مدیریت</span></div>
            <div class="admin-topbar-actions"><button class="admin-icon-btn" type="button" title="جستجو"><i class="fas fa-search"></i></button><button class="admin-icon-btn" type="button" title="تغییر حالت" onclick="document.body.classList.toggle('theme-dark')"><i class="fas fa-moon"></i></button><form action="{{ route('logout') }}" method="post"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button class="admin-icon-btn admin-logout" type="submit" title="خروج"><i class="fas fa-right-from-bracket"></i></button></form><button id="menuBtn" class="admin-icon-btn mobile-menu" type="button" title="منو"><i class="fas fa-bars"></i></button></div>
        </header>
        <div class="content">
            @yield('content')
        </div>
    </main>
</div>

<script>
    // مدیریت باز و بسته شدن سایدبار در موبایل
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menuBtn');
    const overlay = document.getElementById('overlay');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    if (menuBtn) {
        menuBtn.addEventListener('click', openSidebar);
    }
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });

    sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });
</script>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>