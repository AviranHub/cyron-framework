<!DOCTYPE html>
<html lang="{{ locale() }}" dir="rtl" x-data="appComponent()" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords"
        content="کلبه کتاب,دنیای کتاب و رمان و کتاب صوتی,برنامه نویسی,programming,طراحی سایت,web design,تیم,team,">
    <meta name="description" content="کلبه کتاب: بزرگترین مرجع دانلود و مطالعه کتاب‌های الکترونیک و رمان‌های فارسی. با هزاران کتاب رایگان و پرفروش در ژانرهای مختلف.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle ?? 'کلبه کتاب | دنیای کتاب و رمان و کتاب صوتی' }}</title>

    <link rel="shortcut icon" href="/assets/img/icon.png" type="image/png">

    <link rel="stylesheet" href="/assets/css/all.css">
    {{-- <link rel="stylesheet" href="/assets/css/style.css"> --}}
    {{-- <link rel="stylesheet" href="/assets/css/greentyle.css"> --}}


    <link rel="stylesheet" href="/build/assets/style.css">
    <script type="module" src="/build/assets/script.js"></script>

    <link rel="stylesheet" href="http://localhost:5173/resources/Styles/style.css">
    <script type="module" src="http://localhost:5173/resources/Scripts/script.js"></script>

    <!-- <link rel="manifest" href="/manifest.json"> -->

    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="کلبه کتاب">
    <link rel="apple-touch-icon" href="/assets/icons/icon-192x192.png">

    <!-- <link rel="preload" as="style" href="/build/assets/app.css" /> -->
    <!-- <link rel="modulepreload" href="/build/assets/app.js" /> -->
    <!-- <link rel="stylesheet" href="/build/assets/app.css" data-navigate-track="reload" /> -->
    <!-- <script type="module" src="/build/assets/app.js" data-navigate-track="reload"></script> -->

    <!-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> -->

    {{-- <script src="/assets/js/app.js" defer></script> --}}
    {{-- <script src="/assets/js/scipt.js" defer></script> --}}
</head>

<body class="bg-gradient-to-bl from-zinc-50 to-lime-50 dark:bg-gradient-to-br dark:from-zinc-700 dark:via-zinc-800 dark:to-zinc-950">

    @include('layouts.nav')

    <main class="w-full mt-12 md:mt-16">
        @yield('content')
    </main>

    @include('layouts.footer')


    <script>
        function searchBox() {
            return {
                search: '',
                recentSearches: [],
                focused: false, // کنترل نمایش تاریخچه
                init() {
                    this.recentSearches = JSON.parse(localStorage.getItem('recentSearches')) || [];
                },
                saveSearch() {
                    if (!this.search) return;

                    this.recentSearches = this.recentSearches.filter(item => item !== this.search);
                    this.recentSearches.unshift(this.search);
                    if (this.recentSearches.length > 10) this.recentSearches.pop();
                    localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches));
                },
                fillSearch(item) {
                    this.search = item;
                    this.$el.querySelector('input[name="query"]').focus();
                    this.$el.submit();
                    this.$refs.searchForm.submit();
                }
            }
        }
    </script>
    <script>
        function appComponent() {
            return {
                showButton: false,
                darkMode: false,
                // در appComponent اضافه کنید
                showSubscriptionModal: false,
                init() {
                    window.addEventListener('scroll', () => {
                        this.showButton = window.scrollY >
                            300; // دکمه را نشان بدهید اگر بیشتر از 300 پیکسل اسکرول شده باشد
                    });
                    this.darkMode = localStorage.getItem('darkMode') === 'true';
                },
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                },
                scrollToTop() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth' // اسکرول به بالا به صورت نرم
                    });
                }
            }
        }
    </script>

    {{-- @yield('script') --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered', reg))
                    .catch(err => console.log('SW registration failed', err));
            });
        }

        // let deferredPrompt;
        // window.addEventListener('beforeinstallprompt', (e) => {
        //     e.preventDefault();
        //     deferredPrompt = e;
        //     // نمایش دکمه سفارشی "نصب اپ"
        //     const installBtn = document.getElementById('installAppBtn');
        //     if (installBtn) installBtn.style.display = 'block';
        //     installBtn?.addEventListener('click', () => {
        //         installBtn.style.display = 'none';
        //         deferredPrompt.prompt();
        //         deferredPrompt.userChoice.then(choice => {
        //             if (choice.outcome === 'accepted') console.log('نصب شد');
        //             deferredPrompt = null;
        //         });
        //     });
        // });
    </script>


</body>

</html>