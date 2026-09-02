<html lang="fa" dir="rtl" x-data="{ open: false }">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport" />
    <title>@var('APP_NAME')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;700&amp;display=swap" rel="stylesheet" />
    <link rel="icon" href="@asset('/img/logo.png')" type="image/png">
</head>
<body>

    <div class="flex">
        {{-- @include('Layouts.nav') --}}
        <div class="bg-gray-100 w-full">
            <header class="bg-white shadow w-full flex justify-between items-center z-20">
                <div class="w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <h1 class="text-3xl font-bold text-gray-900 text-right">داشبورد</h1>
                        <!-- Mobile menu button -->
                        <div class="sm:hidden">
                            <button @click="open = !open"
                                class="text-gray-500 focus:outline-none focus:text-gray-900">
                                <i :class="{'fa-bars': !open, 'fa-times': open}" class="fas text-2xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </header>
            <main class="mt-16">

                @yield('content')

            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebar', () => ({
                open: false,
            }))
        });
    </script>
    @yield('script')

</body>

</html>