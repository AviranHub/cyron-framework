<header class="fixed top-0 w-full bg-white/70 backdrop-blur-sm dark:bg-zinc-900/70 dark:backdrop-blur-sm shadow-md font-vazir overflow-visible z-20" x-data="{ open: false, openDropdown: false, showSearch: false }">
    <div class="container mx-auto flex justify-between items-center px-6">
        <div class="hidden md:flex items-center">
            <img src="/assets/img/icon.png" class="block h-12 w-auto fill-current text-zinc-800 dark:text-zinc-200" alt="لوگو کلبه کتاب" />
            <p class="text-xl font-bold dark:text-zinc-200">کلبه کتاب</p>
        </div>
        <div x-data="{ searchBoxShow: false }" class="flex md:hidden w-2/3 items-center space-x-4 space-x-reverse py-2">
            <div class="flex relative w-full flex-col py-1 rounded-xl bg-zinc-200 dark:bg-zinc-800 border border-zinc-700">
                <div class="flex w-full" @click="searchBoxShow = true; $nextTick(() => $refs.searchInput.focus())">
                    <button class="text-green-500 px-4 text-lg"><i class="fa fa-search"></i></button>
                    <p class="flex text-gray-900 dark:text-gray-50">جستجو در <img src="{{ asset('img/logo.png') }}" class="h-6 mr-1" alt="کلبه کتاب"></p>
                </div>
            </div>
            <div x-show="searchBoxShow" x-transition class="flex fixed w-full h-screen z-20 bg-gray-50/95 dark:bg-zinc-900/95 backdrop-blur-lg top-0 left-0" @wheel.prevent @touchmove.prevent>
                <form x-data="searchBox()" x-ref="searchForm" method="GET" action="{{ route('search') }}" @submit="saveSearch()" class="flex w-full h-screen flex-col relative px-4">
                    <div class="flex flex-row-reverse w-full p-2">
                        <div class="flex items-center text-gray-600 dark:text-gray-200 text-sm py-2" @click="searchBoxShow = false"><p class="mx-2">بازگشت</p><i class="fa fa-arrow-left"></i></div>
                    </div>
                    <h2 class="text-center text-green-500 text-3xl font-titr my-6">جستجو در کلبه کتاب</h2>
                    <div class="flex w-full border-b-2 border-green-500 w-full h-12 my-2 items-center">
                        <button class="text-green-500 text-lg p-1"><i class="fa fa-search"></i></button>
                        <input x-model="search" x-ref="searchInput" enterkeyhint="search" inputmode="search" @focus="focused = true" class="w-full mx-2 bg-transparent text-gray-800 dark:text-gray-100 caret-green-500 outline-none border-none  focus:outline-none focus:ring-0 focus:shadow-none" type="text" placeholder="جستجو کتاب ، نویسنده و ..." name="query" required>
                        <button type="button" x-show="search" @click="search = ''" class="text-gray-500 dark:text-gray-400 text-lg px-2">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div x-show="recentSearches.length > 0" class="w-full z-20 mt-8 bg-transparent">
                        <p class="my-2 dark:text-gray-50"><i class="far fa-clock ml-2"></i>جستجو های اخیر</p>
                        <div class="w-full z-20 bg-white/70 dark:bg-zinc-900/70 rounded-2xl shadow-xl border border-lime-500 dark:border-zinc-500 py-2 max-h-60 overflow-auto">
                            <template x-for="item in recentSearches" :key="item">
                                <li @click="fillSearch(item)"
                                    class="flex items-center cursor-pointer p-2 hover:bg-lime-50 dark:hover:bg-zinc-600">
                                    <i class="fa fa-history ml-2 text-lime-600 dark:text-lime-500"></i>
                                    <span x-text="item" class="text-black dark:text-white"></span>
                                </li>
                            </template>
                        </div>
                    </div>
                </form>
            </div>
        </div>



        <div class="hidden md:flex w-1/2 items-center space-x-4 space-x-reverse py-3">


            <form x-data="searchBox()" x-init="init()" @submit="saveSearch()" action="{{ route('search') }}"
                method="get"
                class="flex relative w-full flex-col rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-700 focus-within:border-lime-600">
                @csrf
                <div class="flex w-full">
                    <button type="submit" class="text-lime-500 px-4 text-lg"><i class="fa fa-search"></i></button>

                    <input x-model="search" @focus="focused = true" @blur="setTimeout(() => focused = false, 150)"
                        class="bg-transparent w-full py-2 text-lg text-zinc-800 dark:text-zinc-200 border-none outline-none focus:outline-none focus:ring-0 focus:shadow-none"
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="off"
                        spellcheck="false"
                        placeholder="جستجو کتاب ، نویسنده و ..." name="query" type="text" required>

                    <button type="button" x-show="search.length > 0" @click="search = ''"
                        class="text-red-500 px-4 text-lg transition-opacity duration-200">
                        <i class="fa fa-close"></i>
                    </button>
                </div>

                <!-- نمایش تاریخچه فقط وقتی input فوکوس شده -->
                <div x-show="recentSearches.length > 0 && focused && search.length === 0"
                    class="absolute z-10 top-full mt-2 w-full bg-white dark:bg-zinc-700 rounded-2xl shadow-xl border border-lime-500 dark:border-zinc-500 py-2 max-h-60 overflow-auto">
                    <template x-for="item in recentSearches" :key="item">
                        <li @click="fillSearch(item)"
                            class="flex items-center cursor-pointer p-2 hover:bg-lime-50 dark:hover:bg-zinc-600">
                            <i class="fa fa-history ml-2 text-lime-600 dark:text-lime-500"></i>
                            <span x-text="item" class="text-black dark:text-white"></span>
                        </li>
                    </template>
                </div>
            </form>

            {{-- <form x-data="{ search: '' }" action="{{ route('search') }}" method="get"
            class="flex w-full rounded-xl bg-zinc-200 dark:bg-zinc-800 border border-zinc-700">
            @csrf
            <button class="text-green-500 px-4 text-lg"><i class="fa fa-search"></i></button>

            <input x-model="search"
                class="bg-transparent w-full text-lg text-zinc-800 dark:text-zinc-200 border-none outline-none focus:outline-none focus:ring-0 focus:shadow-none"
                placeholder="جستجو کتاب ، نویسنده و ..." name="query" type="text" required>

            <button x-show="search.length > 0" @click="search = ''"
                class="text-red-500 px-4 text-lg transition-opacity duration-200">
                <i class="fa fa-close"></i>
            </button>
            </form> --}}

        </div>
        <div class="flex items-center space-x-4 space-x-reverse">
            <button class="bg-lime-100 rounded-full w-8 h-8 justify-center items-center inline-flex dark:bg-zinc-900" :class="darkMode ? 'text-yellow-500' : 'text-lime-600'" title="تنظیم تم" @click="toggleDarkMode()">
                <i :class="darkMode ? 'fa fa-sun' : 'fa fa-moon'"></i>
            </button>
            @auth
            <a href="{{ route('dashboard') }}" class="flex text-zinc-400 dark:bg-zinc-800 py-2 px-4 rounded-lg border border-green-500 dark:border-zinc-400" title="داشبورد"><i
                    class="fa fa-user md:ml-2"></i>
                <p class="hidden md:block">پنل کاربری</p>
            </a>
            @else
            <a href="{{ route('login') }}" class="text-zinc-500" title="ورود | ثبت نام"><i
                    class="fa-solid fa-right-to-bracket"></i></a>
            @endauth
            {{-- <button class="text-zinc-500" title="نمایش سرچ باکس" @click="showSearch = true">
                <i class="fas fa-search"></i>
            </button> --}}
            <button class="md:hidden text-zinc-500" @click="open = !open">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    <div x-data="{ lastScroll: 0, showNav: true }" x-init="window.addEventListener('scroll', () => {
        let current = window.pageYOffset;
        if (current > lastScroll) {
            showNav = false;
        } else {
            showNav = true;
        }
        lastScroll = current;
    })">
        <div x-show="showNav" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="container mx-auto flex justify-between items-center px-6 border-t border-zinc-700">
            <nav class="hidden md:flex items-center space-x-4 space-x-reverse">


                <x-bar-a :href="route('home')" :active="request()->routeIs('home')">
                    {{ __('Home') }}
                </x-bar-a>

                <div
                    class="relative group py-2 px-5 border-b-2 border-transparent font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out">
                    <p class="flex items-center cursor-pointer">
                        دسته بندی
                        <i class="fa fa-chevron-down mr-2 text-sm"></i>
                    </p>

                    <!-- مگا منو -->
                    <div
                        class="absolute right-0 top-full hidden group-hover:flex bg-white dark:bg-zinc-700 shadow-lg rounded-lg w-[700px] p-6 z-50">
                        <div class="grid grid-cols-3 gap-6 w-full">

                            <!-- ستون اول -->
                            <div>
                                <h3 class="font-semibold text-blue-600 dark:text-blue-400 mb-3">📚 کتاب‌ها</h3>
                                <ul class="space-y-2">
                                    <li><a href="#" class="block hover:text-blue-500">رمان</a></li>
                                    <li><a href="#" class="block hover:text-blue-500">علمی</a></li>
                                    <li><a href="#" class="block hover:text-blue-500">تاریخی</a></li>
                                    <li><a href="#" class="block hover:text-blue-500">هنری</a></li>
                                </ul>
                            </div>

                            <!-- ستون دوم -->
                            <div>
                                <h3 class="font-semibold text-green-600 dark:text-green-400 mb-3">🎯 فیلترها</h3>
                                <ul class="space-y-2">
                                    <li><a href="#" class="block hover:text-green-500">پرفروش‌ها</a></li>
                                    <li><a href="#" class="block hover:text-green-500">جدیدترین</a></li>
                                    <li><a href="#" class="block hover:text-green-500">با تخفیف</a></li>
                                    <li><a href="#" class="block hover:text-green-500">بر اساس امتیاز</a></li>
                                </ul>
                            </div>

                            <!-- ستون سوم -->
                            <div>
                                <h3 class="font-semibold text-red-600 dark:text-red-400 mb-3">⭐ ویژه</h3>
                                <div class="rounded-lg overflow-hidden shadow-md">
                                    <img src="/assets/img/icon.png" alt="کتاب ویژه"
                                        class="w-full h-auto" />
                                </div>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">بهترین انتخاب هفته</p>
                            </div>

                        </div>
                    </div>
                </div>

                <x-bar-a :href="route('books')" :active="request()->routeIs('books')">
                    {{ __('Books') }}
                </x-bar-a>

                <x-bar-a :href="route('blog')" :active="request()->routeIs('blog')">
                    {{ __('Blog') }}
                </x-bar-a>
                <x-bar-a :href="route('about-us')" :active="request()->routeIs('about-us')">
                    {{ __('About Us') }}
                </x-bar-a>
                <x-bar-a :href="route('contact-us')" :active="request()->routeIs('contact-us')">
                    {{ __('Contact Us') }}
                </x-bar-a>

                <button
                    @click="showSubscriptionModal = true"
                    class="group relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-lg bg-gradient-to-r from-lime-300 to-lime-600 px-2 py-1 text-md text-black shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-lime-400 focus:ring-offset-2">
                    <i class="fa fa-gem text-cyan-100 transition-transform duration-300 group-hover:rotate-12"></i>
                    <span>اشتراک ویژه</span>
                </button>



            </nav>
        </div>
    </div>


    <div class="md:hidden fixed inset-0 w-full h-screen flex z-90" x-show="open">
        <div class="w-3/4 bg-white dark:bg-zinc-900">
            <nav>
                <a class="block px-4 py-2 text-zinc-900 font-bold border-r-4 border-green-700 bg-green-200 active-link-mobile"
                    href="{{ route('home') }}">صفحه اصلی</a>
                <x-responsive-nav-a :href="route('home')" :active="request()->routeIs('home')">
                    {{ __('Home') }}
                </x-responsive-nav-a>
                <x-responsive-nav-a :href="route('books')" :active="request()->routeIs('books')">
                    {{ __('Books') }}
                </x-responsive-nav-a>
                <x-responsive-nav-a :href="route('blog')" :active="request()->routeIs('blog')">
                    {{ __('Blog') }}
                </x-responsive-nav-a>
                <x-responsive-nav-a :href="route('about-us')" :active="request()->routeIs('about-us')">
                    {{ __('About Us') }}
                </x-responsive-nav-a>
                <x-responsive-nav-a :href="route('contact-us')" :active="request()->routeIs('contact-us')">
                    {{ __('Contact Us') }}
                </x-responsive-nav-a>
                <x-responsive-nav-a :href="route('contact-us')" :active="request()->routeIs('contact-us')">
                    تالار گفتگو
                </x-responsive-nav-a>

                {{-- <div class="relative" x-data="{ open: false }">
                    <button
                        class="flex justify-between items-center w-full text-left px-4 py-2 text-zinc-700 hover:text-green-600"
                        @click="open = !open">
                        <span>کتاب</span>
                        <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                    </button>
                    <div class="bg-zinc-200 rounded-md p-2 mt-2 mx-4" x-show="open" @click.away="open = false">
                        <a class="block px-4 py-2 text-zinc-700 hover:bg-zinc-300" href="#">رمان</a>
                        <a class="block px-4 py-2 text-zinc-700 hover:bg-zinc-300" href="#">داستان کوتاه</a>
                        <a class="block px-4 py-2 text-zinc-700 hover:bg-zinc-300" href="#">شعر</a>
                    </div>
                </div> --}}
            </nav>
        </div>

        <div class="absolute left-0 w-1/4 h-full bg-zinc-900/50 backdrop-blur" @click="open = false"></div>
    </div>

    <!-- Popup Search Box -->
    <div class="fixed inset-0 h-screen bg-zinc-800 bg-opacity-75 flex items-center justify-center z-50" style="display: none;"
        x-show="showSearch" @click.away="showSearch = false">
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-lg shadow-lg w-11/12 md:w-1/2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">جستجو</h2>
                <button class="text-zinc-500 dark:text-zinc-400" @click="showSearch = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('search') }}" method="get">
                @csrf
                <input
                    class="border border-zinc-300 dark:border-zinc-600 rounded-md w-full px-4 py-2 text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-800 placeholder-zinc-500 dark:placeholder-zinc-400"
                    placeholder="دنبال چه کتابی میگردی؟" name="query" type="text" required />
                <button class="bg-green-600 text-white dark:bg-green-700 px-4 py-2 rounded-md mt-4 w-full"
                    type="submit">جستجو</button>
            </form>
        </div>
    </div>
</header>