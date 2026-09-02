<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="کلبه کتاب؛ بزرگترین پلتفرم کتاب الکترونیک و رمان در ایران. دانلود و مطالعه هزاران کتاب رایگان و پرفروش.">
    <meta name="keywords" content="کلبه کتاب, کتاب الکترونیک, رمان, کتاب صوتی, دانلود کتاب, فروشگاه کتاب آنلاین">
    <meta name="csrf-token" content="I109zF5WN7w8SNMLsIYrMCw3ELOQefztYUEFe9RC">
    <title>کلبه کتاب | دریچهای به دنیای داستان و دانش</title>
    <link rel="shortcut icon" href="/assets/img/icon.png" type="image/png">
    
    <!-- Tailwind + Fonts + Libraries -->
    <!-- <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="http://localhost:5173/resources/Styles/style.css">
    <script type="module" src="http://localhost:5173/resources/Scripts/script.js"></script>

    <style>
        /* تنظیم فونت فارسی وزیر با fallback مناسب */
        @font-face {
            font-family: 'Vazir';
            src: url('https://cdn.fontcdn.ir/Font/Persian/Vazir/Vazir.eot');
            src: url('https://cdn.fontcdn.ir/Font/Persian/Vazir/Vazir.eot?#iefix') format('embedded-opentype'),
                 url('https://cdn.fontcdn.ir/Font/Persian/Vazir/Vazir.woff2') format('woff2'),
                 url('https://cdn.fontcdn.ir/Font/Persian/Vazir/Vazir.woff') format('woff');
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'Vazir', 'Inter', system-ui, -apple-system, sans-serif;
        }
        /* اسکرول سفارشی مدرن */
        .custom-scroll::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #c7c7c7;
            border-radius: 10px;
        }
        .dark .custom-scroll::-webkit-scrollbar-track {
            background: #2d2d2d;
        }
        .dark .custom-scroll::-webkit-scrollbar-thumb {
            background: #555;
        }
        /* انیمیشن شناور کتاب مرکزی */
        @keyframes floatBook {
            0% { transform: translateX(-50%) translateY(0px); }
            50% { transform: translateX(-50%) translateY(-12px); }
            100% { transform: translateX(-50%) translateY(0px); }
        }
        .animate-float-center {
            animation: floatBook 4s ease-in-out infinite;
        }
        /* جلوه شیشه‌ای مدرن برای هدر و کارت‌ها */
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.3);
        }
        .dark .glass-nav {
            background: rgba(24, 24, 27, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        /* کارت کتاب hover */
        .book-card {
            transition: all 0.25s cubic-bezier(0.2, 0, 0, 1);
        }
        .book-card:hover {
            transform: translateY(-6px);
        }
        .hero-gradient {
            background: radial-gradient(ellipse at 30% 40%, rgba(245,158,11,0.08), rgba(21,128,61,0.02));
        }
        .dark .hero-gradient {
            background: radial-gradient(ellipse at 30% 40%, rgba(245,158,11,0.12), rgba(21,128,61,0.05));
        }
        /* منوی مگا منو دسکتاپ */
        .mega-menu {
            transition: opacity 0.2s, visibility 0.2s;
        }
        .group:hover .mega-menu {
            visibility: visible;
            opacity: 1;
        }
        /* ریسپانسیو adjustments */
        @media (max-width: 768px) {
            .hero-carousel-container {
                scale: 0.9;
            }
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        vazir: ['Vazir', 'Inter'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                    }
                }
            }
        }
    </script>
</head>
<body x-data="app()" :class="{ 'dark': darkMode }" class="bg-amber-50/40 dark:bg-zinc-900 transition-colors duration-300">

    <!-- ========== HEADER GLASS + STICKY ========== -->
    <header x-data="{ mobileMenuOpen: false, lastScroll: 0, showHeader: true }" 
            x-init="window.addEventListener('scroll', () => { let st = window.pageYOffset; showHeader = (st < lastScroll || st < 80); lastScroll = st; })"
            :class="showHeader ? 'translate-y-0' : '-translate-y-full'"
            class="fixed top-0 w-full z-50 transition-transform duration-500 glass-nav shadow-sm">
        <div class="container mx-auto px-5 md:px-8 py-2 flex items-center justify-between">
            <!-- لوگو + نام -->
            <a href="/" class="flex items-center gap-2 group">
                <img src="/assets/img/icon.png" class="h-9 w-auto drop-shadow-md" alt="کلبه کتاب">
                <span class="text-xl font-bold bg-gradient-to-l from-amber-700 to-amber-600 dark:from-amber-400 dark:to-amber-300 bg-clip-text text-transparent">کلبه کتاب</span>
            </a>

            <!-- سرچ دسکتاپ (مخفی در موبایل) -->
            <div class="hidden md:flex w-full max-w-md mx-6" x-data="searchBox()" x-init="init()">
                <form @submit.prevent="saveSearchAndSubmit" action="https://bkhut.ir/search" method="get" class="relative w-full">
                    <div class="flex items-center bg-white/70 dark:bg-zinc-800/80 rounded-full shadow-inner border border-amber-200 dark:border-zinc-700 focus-within:ring-2 focus-within:ring-amber-400 transition-all">
                        <button type="submit" class="text-amber-600 dark:text-amber-400 pr-4 pl-2 text-lg"><i class="fas fa-search"></i></button>
                        <input type="text" name="query" x-model="search" @focus="focused=true" @blur="setTimeout(()=> focused=false, 200)" 
                               class="w-full py-2.5 bg-transparent text-zinc-800 dark:text-zinc-100 outline-none text-sm" placeholder="جستجو کتاب، نویسنده، موضوع...">
                        <button type="button" x-show="search.length" @click="search=''" class="text-red-400 px-3"><i class="fas fa-times-circle"></i></button>
                    </div>
                    <!-- لیست تاریخچه جستجو -->
                    <div x-show="recentSearches.length > 0 && focused && search.length === 0" x-cloak class="absolute top-full mt-2 w-full bg-white/95 dark:bg-zinc-800/95 backdrop-blur-sm rounded-2xl shadow-xl border border-amber-100 dark:border-zinc-700 z-30 py-2">
                        <template x-for="item in recentSearches" :key="item">
                            <div @click="fillSearch(item)" class="flex items-center gap-3 px-4 py-2 hover:bg-amber-50 dark:hover:bg-zinc-700 cursor-pointer text-sm">
                                <i class="fas fa-history text-amber-400 text-xs"></i>
                                <span x-text="item" class="text-zinc-700 dark:text-zinc-300"></span>
                            </div>
                        </template>
                    </div>
                </form>
            </div>

            <!-- actions: دکمه دارک مود + پنل کاربری + منو همبرگر -->
            <div class="flex items-center gap-3">
                <button @click="toggleDarkMode" class="w-9 h-9 rounded-full flex items-center justify-center bg-amber-100 dark:bg-zinc-800 text-amber-600 dark:text-amber-400 transition-all hover:scale-105">
                    <i :class="darkMode ? 'fas fa-sun' : 'fas fa-moon'"></i>
                </button>
                <a href="https://bkhut.ir/dashboard" class="hidden md:flex items-center gap-2 border border-amber-300 dark:border-zinc-700 rounded-full px-4 py-1.5 text-sm font-medium hover:bg-amber-100 dark:hover:bg-zinc-800 transition">
                    <i class="fas fa-user-circle text-amber-600"></i> <span>پنل کاربری</span>
                </a>
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="block md:hidden text-2xl text-amber-700 dark:text-amber-300">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- منوی دسکتاپ (ناوبری اصلی) -->
        <div class="hidden md:block border-t border-amber-100/50 dark:border-zinc-800">
            <div class="container mx-auto px-8 flex items-center justify-end gap-8 text-sm font-medium">
                <a href="https://bkhut.ir" class="py-3 text-amber-800 dark:text-amber-300 border-b-2 border-amber-500">خانه</a>
                
                <!-- دسته‌بندی با مگامنو -->
                <div class="relative group py-3">
                    <button class="flex items-center gap-1 text-zinc-700 dark:text-zinc-300 group-hover:text-amber-600 transition">دسته‌بندی <i class="fas fa-chevron-down text-xs"></i></button>
                    <div class="mega-menu invisible opacity-0 group-hover:visible group-hover:opacity-100 absolute right-0 top-full w-[680px] bg-white/95 dark:bg-zinc-800/95 backdrop-blur-md shadow-2xl rounded-2xl p-5 mt-2 transition-all duration-200 border border-amber-100 dark:border-zinc-700 z-40">
                        <div class="grid grid-cols-3 gap-6">
                            <div><h4 class="font-bold text-amber-700 dark:text-amber-400 mb-2">📚 ژانرهای پرطرفدار</h4><ul class="space-y-2 text-sm"><li><a href="#" class="hover:text-amber-600">رمان عاشقانه</a></li><li><a href="#" class="hover:text-amber-600">ادبیات کلاسیک</a></li><li><a href="#" class="hover:text-amber-600">تاریخی</a></li><li><a href="#" class="hover:text-amber-600">علمی تخیلی</a></li></ul></div>
                            <div><h4 class="font-bold text-amber-700 dark:text-amber-400 mb-2">🎯 ویژه</h4><ul class="space-y-2 text-sm"><li><a href="#" class="hover:text-amber-600">پرفروش‌ترین‌ها</a></li><li><a href="#" class="hover:text-amber-600">تازه‌های نشر</a></li><li><a href="#" class="hover:text-amber-600">کتاب‌های رایگان</a></li><li><a href="#" class="hover:text-amber-600">با تخفیف ویژه</a></li></ul></div>
                            <div><div class="rounded-xl overflow-hidden shadow-md"><img src="https://via.placeholder.com/200x120/FFE0B5/AA6C39?text=کتاب+هفته" class="w-full"></div><p class="text-xs text-zinc-500 mt-2">کتاب برگزیده هفته: بادبادک‌باز</p></div>
                        </div>
                    </div>
                </div>
                <a href="https://bkhut.ir/books" class="py-3 text-zinc-700 dark:text-zinc-300 hover:text-amber-600">کتاب‌ها</a>
                <a href="https://bkhut.ir/blog" class="py-3 text-zinc-700 dark:text-zinc-300 hover:text-amber-600">بلاگ</a>
                <a href="https://bkhut.ir/about-us" class="py-3 text-zinc-700 dark:text-zinc-300 hover:text-amber-600">درباره ما</a>
                <a href="https://bkhut.ir/contact-us" class="py-3 text-zinc-700 dark:text-zinc-300 hover:text-amber-600">تماس با ما</a>
            </div>
        </div>

        <!-- منوی موبایل (Slide-in) -->
        <div x-show="mobileMenuOpen" x-transition.opacity class="fixed inset-0 bg-black/40 z-50 md:hidden" @click="mobileMenuOpen=false"></div>
        <div x-show="mobileMenuOpen" x-transition:enter="transition duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed top-0 right-0 w-3/4 max-w-xs h-full bg-white dark:bg-zinc-900 shadow-2xl z-50 p-5 flex flex-col gap-4">
            <div class="flex justify-between items-center border-b pb-3"><span class="font-bold">کلبه کتاب</span><button @click="mobileMenuOpen=false"><i class="fas fa-times text-xl"></i></button></div>
            <nav class="flex flex-col gap-3 text-base"><a href="https://bkhut.ir">خانه</a><a href="https://bkhut.ir/books">کتاب‌ها</a><a href="https://bkhut.ir/blog">بلاگ</a><a href="https://bkhut.ir/about-us">درباره ما</a><a href="https://bkhut.ir/contact-us">تماس با ما</a><a href="https://bkhut.ir/dashboard" class="mt-4 flex items-center gap-2 text-amber-600"><i class="fas fa-user"></i> پنل کاربری</a></nav>
        </div>
    </header>

    <main class="pt-28 md:pt-32">
        <!-- ========== HERO SECTION با کاروسل سه کتاب مدرن ========== -->
        <section class="hero-gradient relative overflow-hidden py-10 md:py-16">
            <div class="container mx-auto px-5 flex flex-col-reverse lg:flex-row items-center gap-8">
                <div class="lg:w-1/2 text-center lg:text-right animate-fade-in">
                    <span class="inline-block bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 rounded-full px-4 py-1 text-sm mb-4">✨ بیش از ۱۰۰۰۰ کتاب الکترونیک</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight bg-gradient-to-l from-amber-800 to-amber-600 dark:from-amber-300 dark:to-amber-400 bg-clip-text text-transparent">کلبه کتاب، <br>دنیای بی‌پایان داستان‌ها</h1>
                    <p class="text-zinc-600 dark:text-zinc-300 text-lg mt-6 max-w-lg mx-auto lg:mx-0">بهترین کتاب‌ها، رمان‌های پرفروش و دانلود رایگان — هر آنچه یک کتابخوان نیاز دارد اینجاست.</p>
                    <div class="flex flex-wrap justify-center lg:justify-start gap-4 mt-8">
                        <a href="https://bkhut.ir/register" class="bg-amber-500 hover:bg-amber-600 text-white px-8 py-3 rounded-full shadow-lg transition-all hover:scale-105 font-bold">عضویت رایگان <i class="fas fa-arrow-left mr-1"></i></a>
                        <a href="https://bkhut.ir/books" class="border border-amber-300 dark:border-zinc-600 rounded-full px-8 py-3 font-medium hover:bg-amber-50 dark:hover:bg-zinc-800 transition">مشاهده کتاب‌ها</a>
                    </div>
                </div>
                <!-- کاروسل سه بعدی کتاب‌ها -->
                <div class="lg:w-1/2 flex justify-center relative h-[280px] md:h-[340px] hero-carousel-container" x-data="carousel3D()" x-init="initCarousel">
                    <template x-for="(book, idx) in books" :key="idx">
                        <a :href="book.url" class="absolute transition-all duration-700 ease-out cursor-pointer group" :class="getPositionClass(idx)" :style="getStyle(idx)">
                            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-2xl overflow-hidden w-full h-full transform transition hover:scale-105">
                                <img :src="book.img" class="w-full h-full object-cover" :alt="book.title">
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </section>

        <!-- ========== اسلایدرهای کتاب ========== -->
        <!-- تابع کمک برای اسکرول افقی با دکمه‌ها -->
        <div class="container mx-auto px-5 my-12 space-y-14">
            <!-- جدیدترین‌ها -->
            <div x-data="{ scrollId: 'newestScroll' }">
                <div class="flex justify-between items-center mb-5 border-b border-amber-200 dark:border-zinc-800 pb-2">
                    <h2 class="text-2xl font-bold flex items-center gap-2"><i class="fas fa-fire text-amber-500"></i> جدیدترین کتاب‌ها</h2>
                    <a href="https://bkhut.ir/books/category/newest" class="text-amber-600 hover:underline text-sm">مشاهده همه <i class="fas fa-chevron-left mr-1 text-xs"></i></a>
                </div>
                <div class="relative group">
                    <div :id="scrollId" class="flex overflow-x-auto gap-5 pb-4 custom-scroll snap-x snap-mandatory scroll-smooth">
                        <template x-for="book in booksData.newest" :key="book.id">
                            <div class="snap-start shrink-0 w-40 md:w-48 book-card">
                                <a :href="book.url"><img :src="book.img" class="rounded-2xl shadow-lg w-full h-56 md:h-64 object-cover"><div class="mt-2 text-center"><p class="font-bold text-sm truncate" x-text="book.title"></p><p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="book.author"></p><span class="text-green-600 text-xs font-semibold" x-html="book.price"></span></div></a>
                            </div>
                        </template>
                    </div>
                    <button @click="scrollSlider(scrollId, -280)" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 bg-white/80 dark:bg-zinc-800 shadow-lg rounded-full p-2 opacity-0 group-hover:opacity-100 transition"><i class="fas fa-chevron-right text-amber-600"></i></button>
                    <button @click="scrollSlider(scrollId, 280)" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 bg-white/80 dark:bg-zinc-800 shadow-lg rounded-full p-2 opacity-0 group-hover:opacity-100 transition"><i class="fas fa-chevron-left text-amber-600"></i></button>
                </div>
            </div>

            <!-- کتاب‌های رایگان -->
            <div x-data="{ scrollId: 'freeScroll' }">
                <div class="flex justify-between items-center mb-5 border-b border-amber-200 dark:border-zinc-800 pb-2"><h2 class="text-2xl font-bold"><i class="fas fa-gift text-amber-500 ml-2"></i> کتاب‌های رایگان</h2><a href="https://bkhut.ir/books/category/free" class="text-amber-600 text-sm">مشاهده همه</a></div>
                <div class="relative group"><div :id="scrollId" class="flex overflow-x-auto gap-5 pb-4 custom-scroll"><template x-for="book in booksData.free" :key="book.id"><div class="shrink-0 w-40 md:w-48 book-card"><a :href="book.url"><img :src="book.img" class="rounded-2xl shadow w-full h-56 md:h-64 object-cover"><div class="mt-2 text-center"><p class="font-bold truncate" x-text="book.title"></p><span class="text-green-600 text-xs">رایگان</span></div></a></div></template></div><button @click="scrollSlider(scrollId, -280)" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 bg-white/80 rounded-full p-2 opacity-0 group-hover:opacity-100"><i class="fas fa-chevron-right"></i></button><button @click="scrollSlider(scrollId, 280)" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 bg-white/80 rounded-full p-2 opacity-0 group-hover:opacity-100"><i class="fas fa-chevron-left"></i></button></div>
            </div>

            <!-- رمان‌ها -->
            <div x-data="{ scrollId: 'romanScroll' }">
                <div class="flex justify-between items-center mb-5 border-b border-amber-200 dark:border-zinc-800 pb-2"><h2 class="text-2xl font-bold"><i class="fas fa-book-open text-amber-500 ml-2"></i> پرفروش‌ترین رمان‌ها</h2><a href="https://bkhut.ir/books/category/roman" class="text-amber-600 text-sm">مشاهده همه</a></div>
                <div class="relative group"><div :id="scrollId" class="flex overflow-x-auto gap-5 pb-4 custom-scroll"><template x-for="book in booksData.roman" :key="book.id"><div class="shrink-0 w-40 md:w-48"><a :href="book.url"><img :src="book.img" class="rounded-2xl shadow w-full h-56 md:h-64 object-cover"><div class="mt-1 text-center"><p class="font-semibold text-sm truncate" x-text="book.title"></p><span class="text-xs" x-text="book.price"></span></div></a></div></template></div><button @click="scrollSlider(scrollId, -280)" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 bg-white/80 rounded-full p-2 opacity-0 group-hover:opacity-100"><i class="fas fa-chevron-right"></i></button><button @click="scrollSlider(scrollId, 280)" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 bg-white/80 rounded-full p-2 opacity-0 group-hover:opacity-100"><i class="fas fa-chevron-left"></i></button></div>
            </div>
        </div>

        <!-- بنر پیشنهاد ویژه مدرن -->
        <div class="container mx-auto px-5 my-12">
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-3xl shadow-xl overflow-hidden relative">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative p-8 md:p-12 flex flex-col md:flex-row justify-between items-center gap-6 text-white">
                    <div><h3 class="text-3xl font-bold">کتاب هفته با ۳۰٪ تخفیف</h3><p class="text-amber-100">«هنر ظریف بی‌خیالی» اثر مارک منسون</p></div>
                    <a href="#" class="bg-white text-amber-700 px-8 py-3 rounded-full font-bold shadow-lg hover:scale-105 transition">مشاهده و خرید</a>
                </div>
            </div>
        </div>
    </main>

    <!-- ========== فوتر مدرن با گرادینت ========== -->
    <footer class="bg-gradient-to-tr from-zinc-900 to-zinc-800 text-white mt-20 pt-12 pb-6">
        <div class="container mx-auto px-5 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div><h4 class="text-xl font-bold mb-4 relative inline-block">کلبه کتاب<span class="absolute -bottom-2 right-0 w-8 h-0.5 bg-amber-400 rounded-full"></span></h4><p class="text-zinc-300 text-sm leading-relaxed">بزرگترین مرجع کتاب الکترونیک، رمان و کتاب صوتی در ایران. همراه با تجربه کاربری لذت‌بخش.</p><div class="flex gap-3 mt-4"><a href="#" class="bg-zinc-800 p-2 rounded-full hover:bg-amber-500 transition"><i class="fab fa-instagram"></i></a><a href="#" class="bg-zinc-800 p-2 rounded-full hover:bg-amber-500 transition"><i class="fab fa-telegram"></i></a><a href="#" class="bg-zinc-800 p-2 rounded-full hover:bg-amber-500 transition"><i class="fab fa-twitter"></i></a></div></div>
            <div><h4 class="font-bold mb-4">دسترسی سریع</h4><ul class="space-y-2 text-sm"><li><a href="https://bkhut.ir/blog" class="hover:text-amber-400">بلاگ</a></li><li><a href="https://bkhut.ir/about-us" class="hover:text-amber-400">درباره ما</a></li><li><a href="https://bkhut.ir/contact-us" class="hover:text-amber-400">تماس با ما</a></li><li><a href="https://bkhut.ir/login" class="hover:text-amber-400">ورود / ثبت نام</a></li></ul></div>
            <div><h4 class="font-bold mb-4">دسته‌های محبوب</h4><ul class="space-y-2 text-sm"><li><a href="#" class="hover:text-amber-400">رمان عاشقانه</a></li><li><a href="#" class="hover:text-amber-400">ادبیات کلاسیک</a></li><li><a href="#" class="hover:text-amber-400">توسعه فردی</a></li><li><a href="#" class="hover:text-amber-400">کتاب صوتی</a></li></ul></div>
            <div><h4 class="font-bold mb-4">تماس با ما</h4><ul class="space-y-2 text-sm"><li class="flex items-center gap-2"><i class="fas fa-map-marker-alt text-amber-400"></i> تهران، سعادت‌آباد</li><li class="flex items-center gap-2"><i class="fas fa-phone-alt text-amber-400"></i> 021-12345678</li><li class="flex items-center gap-2"><i class="fas fa-envelope text-amber-400"></i> support@bkhut.ir</li></ul></div>
        </div>
        <div class="text-center text-zinc-500 text-sm border-t border-zinc-800 mt-10 pt-6">تمامی حقوق متعلق به کلبه کتاب است © ۱۴۰۴</div>
    </footer>

    <!-- دکمه بازگشت به بالا -->
    <button @click="scrollToTop" x-show="showScroll" x-transition.opacity.duration.300ms class="fixed bottom-6 left-6 bg-amber-500 text-white p-3 rounded-full shadow-xl hover:bg-amber-600 transition-all z-40"><i class="fas fa-arrow-up"></i></button>

    <script>
        // اپلیکیشن اصلی آلپاین
        function app() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true' || false,
                showScroll: false,
                init() {
                    window.addEventListener('scroll', () => { this.showScroll = window.scrollY > 400; });
                    if (this.darkMode) document.documentElement.classList.add('dark');
                },
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    if (this.darkMode) document.documentElement.classList.add('dark');
                    else document.documentElement.classList.remove('dark');
                },
                scrollToTop() { window.scrollTo({ top: 0, behavior: 'smooth' }); },
                scrollSlider(id, distance) {
                    const el = document.getElementById(id);
                    if (el) el.scrollBy({ left: distance, behavior: 'smooth' });
                }
            }
        }

        // جستجو با history
        function searchBox() {
            return {
                search: '',
                recentSearches: [],
                focused: false,
                init() {
                    let stored = localStorage.getItem('recentSearches');
                    this.recentSearches = stored ? JSON.parse(stored) : [];
                },
                saveSearchAndSubmit() {
                    if (!this.search.trim()) return;
                    let updated = [this.search, ...this.recentSearches.filter(s => s !== this.search)].slice(0, 8);
                    this.recentSearches = updated;
                    localStorage.setItem('recentSearches', JSON.stringify(updated));
                    this.$el.submit();
                },
                fillSearch(item) {
                    this.search = item;
                    this.$el.querySelector('input[name="query"]').focus();
                }
            }
        }

        // کاروسل سه بعدی هیرو
        function carousel3D() {
            return {
                books: [
                    { img: "https://bkhut.ir/storage/book/1775014773_qvnjRO.jpg", title: "با تو شروع نشده", url: "https://bkhut.ir/book/ba-to-shroaa-nshdh" },
                    { img: "https://bkhut.ir/storage/book/Screenshot_2024-03-26_200504.png", title: "من تا ابد در بحر", url: "https://bkhut.ir/book/man-ta-abad-dar-bahr" },
                    { img: "https://bkhut.ir/storage/book/orangem.jpg", title: "رمان مربای پرتقال", url: "https://bkhut.ir/book/Orange-jam" }
                ],
                activeIndex: 1,
                interval: null,
                initCarousel() {
                    this.rotate();
                    this.interval = setInterval(() => { this.rotate(); }, 4500);
                },
                rotate() {
                    this.activeIndex = (this.activeIndex + 1) % this.books.length;
                },
                getPositionClass(idx) {
                    let diff = (idx - this.activeIndex + 3) % 3;
                    if (diff === 0) return "book-center";
                    if (diff === 1) return "book-right";
                    return "book-left";
                },
                getStyle(idx) {
                    let base = { transition: "all 0.6s cubic-bezier(0.2,0.9,0.4,1.1)" };
                    if ((idx - this.activeIndex + 3) % 3 === 0) return { ...base, width: "200px", height: "280px", transform: "translateX(-50%) translateY(-10px)", top: "10px", left: "50%", zIndex: 10 };
                    if ((idx - this.activeIndex + 3) % 3 === 1) return { ...base, width: "160px", height: "230px", transform: "translateX(40%) rotateY(15deg)", left: "65%", top: "30px", zIndex: 5 };
                    return { ...base, width: "160px", height: "230px", transform: "translateX(-140%) rotateY(-15deg)", left: "35%", top: "30px", zIndex: 5 };
                }
            }
        }

        // دیتاهای کتاب‌ها (از کد اصلی)
        window.booksData = {
            newest: [
                { id:1, title:"سر پرست وحشی", author:"نامشخص", price:"<span class='text-green-600'>رایگان</span>", img:"https://bkhut.ir/storage/book/1776440621_7JyjB6.jpg", url:"https://bkhut.ir/book/sr-prst-ohshy" },
                { id:2, title:"با تو شروع نشده", author:"مارک ولن", price:"187,000 تومان", img:"https://bkhut.ir/storage/book/1775014773_qvnjRO.jpg", url:"https://bkhut.ir/book/ba-to-shroaa-nshdh" },
                { id:3, title:"فریاد بی صدای عاشقی", author:"علی", price:"59,000 تومان", img:"https://bkhut.ir/storage/book/1707462870_TnfTai.png", url:"https://bkhut.ir/book/fryad-by-sday-aaashky" },
                { id:4, title:"رقبای خطرناک", author:"سعید علیزاده", price:"32,000 تومان", img:"https://bkhut.ir/storage/book/1760121303_Dczwa6.jpg", url:"https://bkhut.ir/book/rkbay-khtrnak" },
                { id:5, title:"پسر عموی من", author:"نامعلوم", price:"رایگان", img:"https://bkhut.ir/storage/book/1750874195_y5pBLa.jpg", url:"https://bkhut.ir/book/psr-aamoy-mn" },
                { id:6, title:"عمق دریای چشمانت", author:"زهرا گلی", price:"رایگان", img:"https://bkhut.ir/storage/book/-2147483648_-218069.jpg", url:"https://bkhut.ir/book/eshk-drayay-chshmant" }
            ],
            free: [
                { id:1, title:"خلاصه عطش بوسه", img:"https://bkhut.ir/storage/book/photo_2023-04-12_11-42-38_nc2vcMy.jpg", url:"https://bkhut.ir/book/Summary-of-thirst-kiss" },
                { id:2, title:"دقایق آخر", img:"https://bkhut.ir/storage/book/images.jpg", url:"https://bkhut.ir/book/last-minutes" },
                { id:3, title:"اسیر دزدان دریایی", img:"https://bkhut.ir/storage/book/IMG_20230415_235747_018.jpg", url:"https://bkhut.ir/book/Captured-by-pirates" },
                { id:4, title:"اشک من و باران", img:"https://bkhut.ir/storage/book/12.png", url:"https://bkhut.ir/book/tears-and-rain" }
            ],
            roman: [
                { id:1, title:"رمان زفیر", price:"49,000 تومان", img:"https://bkhut.ir/storage/book/IMG_20230404_061652_857_qwihfHV.jpg", url:"https://bkhut.ir/book/zafir" },
                { id:2, title:"رمان خلاصه عطش بوسه", price:"رایگان", img:"https://bkhut.ir/storage/book/photo_2023-04-12_11-42-38_nc2vcMy.jpg", url:"https://bkhut.ir/book/Summary-of-thirst-kiss" },
                { id:3, title:"دقایق آخر", price:"رایگان", img:"https://bkhut.ir/storage/book/images.jpg", url:"https://bkhut.ir/book/last-minutes" },
                { id:4, title:"پسر غیرتی", price:"10,000 تومان", img:"https://bkhut.ir/storage/book/IMG_20240729_155230_763.jpg", url:"https://bkhut.ir/book/pesar-gayrati" }
            ]
        };
        document.addEventListener('alpine:init', () => {
            Alpine.data('app', app);
            Alpine.data('searchBox', searchBox);
            Alpine.data('carousel3D', carousel3D);
        });
    </script>
    <script>!function(){var i="Tzj06C",a=window,d=document;function g(){var g=d.createElement("script"),s="https://www.goftino.com/widget/"+i,l=localStorage.getItem("goftino_"+i);g.async=!0,g.src=l?s+"?o="+l:s;d.getElementsByTagName("head")[0].appendChild(g);}"complete"===d.readyState?g():a.attachEvent?a.attachEvent("onload",g):a.addEventListener("load",g,!1);}();</script>
</body>
</html>