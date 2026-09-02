<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@var('APP_NAME')</title>
    <script src="https://cdn.tailwindcss.com">
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;700&amp;display=swap" rel="stylesheet" />
    <link rel="icon" href="@asset('/img/logo.png')" type="image/png">
    <link rel="manifest" href="manifest.json">
</head>

<body>
    @yield('style')

    <div class="bg-white shadow-md overflow-hidden">

        <!-- Header -->
        <div id="header" class="flex items-center justify-between p-4 border-b transition-all duration-300">
            <div class="flex items-center">
                <img class="w-10 h-10" src="@asset('/img/logo.png')" alt="">
                <h1 class="mr-2 text-xl font-bold text-gray-700">@var('APP_NAME')</h1>
            </div>
            <div class="flex items-center">
                <i class="fas fa-plus text-xl text-gray-700 mr-2"></i>
                <a class="text-gray-500 text-sm px-2" href="@route('register')">ثبت صنف</a>

                <!-- دکمه جستجو -->
                <button id="searchButton" class="flex items-center text-gray-700">
                    <i class="fas fa-search text-xl"></i>
                </button>
            </div>
        </div>
        <!-- باکس جستجو -->
        <div id="searchContainer" class="hidden w-full mt-2">
            <form id="searchForm" action="@route('search.results')" method="GET" class="flex items-center bg-white shadow-md p-2">
                <button type="submit" class="bg-rose-500 rounded-lg py-2 px-4 ml-2 transition duration-300 hover:bg-rose-600">
                    <i class="fas fa-search text-white text-lg"></i>
                </button>
                <input type="text" id="searchInput" name="query" class="flex-1 bg-transparent border-none outline-none px-4 py-2 text-lg" placeholder="جستجو..." />
                <button type="button" class="bg-transparent rounded-lg px-2 ml-2 transition duration-300" id="closeButton">
                    <i class="fas fa-close text-black text-2xl"></i>
                </button>
            </form>
        </div>
    </div>

    @yield('content')
    @yield('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchButton = document.getElementById('searchButton');
            const closeButton = document.getElementById('closeButton');
            const searchContainer = document.getElementById('searchContainer');
            const header = document.getElementById('header');
            const searchInput = document.getElementById('searchInput');

            // نمایش یا پنهان کردن باکس جستجو با کلیک بر روی دکمه
            closeButton.addEventListener('click', function(event) {
                event.stopPropagation(); // جلوگیری از بروز رویداد کلیک در والد
                header.classList.remove('hidden'); // مخفی کردن هدر
                searchContainer.classList.add('hidden'); // نمایش باکس جستجو
                searchInput.blur(); // فوکوس بر روی باکس جستجو
            });

            // نمایش یا پنهان کردن باکس جستجو با کلیک بر روی دکمه
            searchButton.addEventListener('click', function(event) {
                event.stopPropagation(); // جلوگیری از بروز رویداد کلیک در والد
                header.classList.add('hidden'); // مخفی کردن هدر
                searchContainer.classList.remove('hidden'); // نمایش باکس جستجو
                searchInput.focus(); // فوکوس بر روی باکس جستجو
            });

            // بستن باکس جستجو اگر کلیک خارج از آن باشد
            document.addEventListener('click', function(event) {
                if (!searchContainer.contains(event.target) && !searchButton.contains(event.target)) {
                    if (!searchContainer.classList.contains('hidden')) {
                        if (searchInput.value.trim() === '') {
                            searchContainer.classList.add('hidden'); // مخفی کردن باکس جستجو
                            header.classList.remove('hidden'); // نمایش دوباره هدر
                        }
                    }
                }
            });

            // ارسال فرم (در صورت نیاز می‌توانید این بخش را تنظیم کنید)
            searchInput.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    const query = searchInput.value;
                    console.log('Searching for:', query);
                    // اینجا می‌توانید کد جستجو را اضافه کنید
                }
            });
        });
    </script>
    <script src="@asset('js/sw.js')" type="application/javascript"></script>
    <script>
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // ایجاد باکس نصب به صورت داینامیک با استفاده از createElement
            const installBox = document.createElement('div');
            installBox.className = "fixed bottom-0 left-0 w-full h-12 bg-white flex items-center justify-between px-4 cursor-pointer";
            installBox.id = "install-box";

            const icon = document.createElement('img');
            icon.alt = "@var('APP_NAME')";
            icon.className = "mr-2";
            icon.height = 24;
            icon.src = "@var('APP_ICON')";
            icon.width = 24;

            const appIcon = document.createElement('i');
            appIcon.className = "fas fa-app";

            const appName = document.createElement('span');
            appName.className = "ml-2";
            appName.textContent = "@var('APP_NAME')";

            const closeButton = document.createElement('button');
            closeButton.className = "text-red-500";
            closeButton.onclick = closeInstallBox;
            closeButton.innerHTML = '<i class="fas fa-times"></i>';

            const flexContainer = document.createElement('div');
            flexContainer.className = "flex items-center";
            flexContainer.appendChild(icon);
            flexContainer.appendChild(appIcon);
            flexContainer.appendChild(appName);

            installBox.appendChild(flexContainer);
            installBox.appendChild(closeButton);
            document.body.appendChild(installBox);

            installBox.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const {
                        outcome
                    } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        console.log('User  accepted the install prompt');
                    } else {
                        console.log('User  dismissed the install prompt');
                    }
                    deferredPrompt = null;
                    installBox.style.display = 'none';
                }
            });

            // پنهان کردن باکس نصب اگر در حالت standalone باشد
            if (window.matchMedia('(display-mode: standalone)').matches) {
                installBox.style.display = 'none';
            }
        });

        function closeInstallBox(event) {
            event.stopPropagation();
            const installBox = document.getElementById('install-box');
            if (installBox) {
                installBox.style.display = 'none';
            }
        }
    </script>
</body>

</html>