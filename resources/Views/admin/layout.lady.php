<!DOCTYPE html>
<html lang="{{ locale() }}" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>پنل مدیریت | </title>
    <link rel="stylesheet" href="/assets/css/all.css">
    <link rel="stylesheet" href="/assets/css/app.css">
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
<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen relative">
    <!-- Sidebar -->
    <aside class="sidebar bg-zinc-800 text-white flex flex-col shadow-xl" id="sidebar">
        <!-- لوگو/عنوان -->
        <div class="text-center py-6 border-b border-zinc-700">
            <h2 class="text-xl font-bold tracking-wide">پنل مدیریت</h2>
            <p class="text-xs text-zinc-400 mt-1">Cyron Framework</p>
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

                <!-- حلقه روی مدل‌های کانفیگ -->
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
        <div class="md:hidden mb-4 flex justify-between items-center">
            <button id="menuBtn" class="bg-indigo-600 text-white p-2 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
            <span class="text-sm text-gray-500">پنل مدیریت</span>
        </div>
        @yield('content')
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