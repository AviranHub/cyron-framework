<html lang="fa" dir="rtl" x-data="{ open: false }">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Web Page
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 flex">
    <!-- Sidebar -->
    <div :class="{'block': open, 'hidden': !open}" class="w-64 bg-white h-screen shadow-md sm:block fixed sm:relative z-40 sm:z-auto">
        <div class="p-4">
            <img src="https://storage.googleapis.com/a1aa/image/PY7MpHmDe9wDTa7r7fX6shXje1wyR8TGG6ya5t5qEMHDjqKnA.jpg" alt="Logo" class="h-8 w-8 mx-auto">
        </div>
        <nav class="mt-10">
            <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200 text-right bg-indigo-100 text-gray-900 border-r-4 border-indigo-500">
                <i class="fas fa-tachometer-alt ml-4"></i>
                داشبورد
            </a>
            <div x-data="{ dropdownOpen: false }">
                <button @click="dropdownOpen = !dropdownOpen" class="w-full text-right block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200">
                    <i class="fas fa-cog ml-4"></i>
                    تنظیمات
                    <i :class="{'fa-chevron-down': !dropdownOpen, 'fa-chevron-up': dropdownOpen}" class="fas float-left"></i>
                </button>
                <div x-show="dropdownOpen" class="pr-8">
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200 text-right">تنظیمات عمومی</a>
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200 text-right">تنظیمات امنیتی</a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Main content -->
    <div class="flex-1">
        <header class="bg-white shadow fixed w-full flex justify-between items-center">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 w-full">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900 text-right">داشبورد</h1>
                    <!-- Mobile menu button -->
                    <div class="sm:hidden">
                        <button @click="open = !open" class="text-gray-500 focus:outline-none focus:text-gray-900">
                            <i :class="{'fa-bars': !open, 'fa-times': open}" class="fas text-2xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </header>
        <main class="mt-16">
            <div class="py-6 sm:px-6 lg:px-8 w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-8">
                    <!-- Card 1 -->
                    <div class="bg-white p-2 rounded-lg shadow h-20 flex items-center">
                        <div class="bg-indigo-100 p-2 rounded-lg ml-3 mr-1 inline-flex items-center justify-center" style="width: 35px; height: 35px;">
                            <i class="fas fa-tasks text-xl text-indigo-500">
                            </i>
                        </div>
                        <div>
                            <h2 class="text-sm">
                                إجمالي المهام
                            </h2>
                            <p class="text-xl font-bold">
                                54
                            </p>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="bg-white p-2 rounded-lg shadow h-20 flex items-center">
                        <div class="bg-indigo-100 p-2 rounded-lg ml-3 mr-1 inline-flex items-center justify-center" style="width: 35px; height: 35px;">
                            <i class="fas fa-clipboard-list text-xl text-indigo-500">
                            </i>
                        </div>
                        <div>
                            <h2 class="text-sm">
                                المهام المتبقية
                            </h2>
                            <p class="text-xl font-bold">
                                23
                            </p>
                        </div>
                    </div>
                    <!-- Card 3 -->
                    <div class="bg-white p-2 rounded-lg shadow h-20 flex items-center">
                        <div class="bg-indigo-100 p-2 rounded-lg ml-3 mr-1 inline-flex items-center justify-center" style="width: 35px; height: 35px;">
                            <i class="fas fa-exclamation-triangle text-xl text-indigo-500">
                            </i>
                        </div>
                        <div>
                            <h2 class="text-sm">
                                المهام المتأخرة
                            </h2>
                            <p class="text-xl font-bold">
                                4
                            </p>
                        </div>
                    </div>
                    <!-- Card 4 -->
                    <div class="bg-white p-2 rounded-lg shadow h-20 flex items-center">
                        <div class="bg-indigo-100 p-2 rounded-lg ml-3 mr-1 inline-flex items-center justify-center" style="width: 35px; height: 35px;">
                            <i class="fas fa-check-circle text-xl text-indigo-500">
                            </i>
                        </div>
                        <div>
                            <h2 class="text-sm">
                                المهام المكتملة
                            </h2>
                            <p class="text-xl font-bold">
                                12
                            </p>
                        </div>
                    </div>
                    <!-- Card 5 -->
                    <div class="bg-white p-3 rounded-lg shadow">
                        <h2 class="text-sm">
                            إجمالي المبيعات
                        </h2>
                        <p class="text-xl font-bold mt-2">
                            $2.5M
                        </p>
                        <div class="mt-4">
                            <img alt="رسم بياني للمبيعات" height="50" src="https://storage.googleapis.com/a1aa/image/ZEVC8kpx6UZrH1gyUmFtHJEwLKNHUmBAcOmfkjUSHbdXTwzJA.jpg" width="100" />
                        </div>
                    </div>
                    <!-- Card 6 -->
                    <div class="bg-white p-3 rounded-lg shadow">
                        <h2 class="text-sm">
                            إجمالي الرصيد
                        </h2>
                        <p class="text-xl font-bold mt-2">
                            $2,123
                        </p>
                        <div class="mt-4">
                            <img alt="رسم بياني للرصيد" height="50" src="https://storage.googleapis.com/a1aa/image/f0ffOSjyHRX9nI7TKdadcjP1TEWf2z8wquCSvOIGqEmBbCecC.jpg" width="100" />
                        </div>
                    </div>
                    <!-- Card 7 -->
                    <div class="bg-white p-3 rounded-lg shadow">
                        <h2 class="text-sm">
                            إجمالي الزوار
                        </h2>
                        <p class="text-xl font-bold mt-2">
                            200K
                        </p>
                        <div class="mt-4">
                            <img alt="رسم بياني للزوار" height="50" src="https://storage.googleapis.com/a1aa/image/zlUgkzVA0Q4ALBN6fxgNnOpVb3aiXRVVM0eNxoyqmDKxmgnTA.jpg" width="100" />
                        </div>
                    </div>
                    <!-- Card 8 -->
                    <div class="bg-white p-3 rounded-lg shadow">
                        <h2 class="text-sm">
                            إجمالي المستخدمين
                        </h2>
                        <p class="text-xl font-bold mt-2">
                            5678
                        </p>
                        <div class="mt-4">
                            <img alt="رسم بياني للمستخدمين" height="50" src="https://storage.googleapis.com/a1aa/image/SpJpInwYIBYXPtYKkipmOSSbadvfOZ6v1o88DrGExgrWTwzJA.jpg" width="100" />
                        </div>
                    </div>
                    <!-- Card 9 -->
                    <div class="bg-white p-3 rounded-lg shadow">
                        <h2 class="text-sm">
                            إجمالي المبيعات
                        </h2>
                        <p class="text-xl font-bold mt-2">
                            $3.6M
                        </p>
                        <div class="mt-4">
                            <img alt="رسم بياني للمبيعات" height="50" src="https://storage.googleapis.com/a1aa/image/ZEVC8kpx6UZrH1gyUmFtHJEwLKNHUmBAcOmfkjUSHbdXTwzJA.jpg" width="100" />
                        </div>
                    </div>
                    <!-- Card 10 -->
                    <div class="bg-white p-3 rounded-lg shadow">
                        <h2 class="text-sm">
                            إجمالي الرصيد
                        </h2>
                        <p class="text-xl font-bold mt-2">
                            $1,123
                        </p>
                        <div class="mt-4">
                            <img alt="رسم بياني للرصيد" height="50" src="https://storage.googleapis.com/a1aa/image/f0ffOSjyHRX9nI7TKdadcjP1TEWf2z8wquCSvOIGqEmBbCecC.jpg" width="100" />
                        </div>
                    </div>
                    <!-- Card 11 -->
                    <div class="bg-white p-3 rounded-lg shadow">
                        <h2 class="text-sm">
                            إجمالي الزوار
                        </h2>
                        <p class="text-xl font-bold mt-2">
                            345K
                        </p>
                        <div class="mt-4">
                            <img alt="رسم بياني للزوار" height="50" src="https://storage.googleapis.com/a1aa/image/zlUgkzVA0Q4ALBN6fxgNnOpVb3aiXRVVM0eNxoyqmDKxmgnTA.jpg" width="100" />
                        </div>
                    </div>
                    <!-- Card 12 -->
                    <div class="bg-white p-3 rounded-lg shadow">
                        <h2 class="text-sm">
                            إجمالي المستخدمين
                        </h2>
                        <p class="text-xl font-bold mt-2">
                            6789
                        </p>
                        <div class="mt-4">
                            <img alt="رسم بياني للمستخدمين" height="50" src="https://storage.googleapis.com/a1aa/image/SpJpInwYIBYXPtYKkipmOSSbadvfOZ6v1o88DrGExgrWTwzJA.jpg" width="100" />
                        </div>
                    </div>
                </div>


                <div class="px-4 py-6 sm:px-0">
                    <div class="border-4 border-dashed border-gray-200 rounded-lg h-96 flex items-center justify-center">
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 text-right">شما وارد شده‌اید!</h3>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-white shadow rounded-lg p-4">
                        <canvas id="chart1"></canvas>
                    </div>
                    <div class="bg-white shadow rounded-lg p-4">
                        <canvas id="chart2"></canvas>
                    </div>
                    <div class="bg-white shadow rounded-lg p-4">
                        <canvas id="chart3"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebar', () => ({
                open: false,
            }))
        });

        // Chart.js initialization
        document.addEventListener('DOMContentLoaded', function() {
            var ctx1 = document.getElementById('chart1').getContext('2d');
            var chart1 = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [{
                        label: 'Dataset 1',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        data: [0, 10, 5, 2, 20, 30, 45]
                    }]
                },
                options: {}
            });

            var ctx2 = document.getElementById('chart2').getContext('2d');
            var chart2 = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [{
                        label: 'Dataset 2',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        data: [10, 20, 30, 40, 50, 60, 70]
                    }]
                },
                options: {}
            });

            var ctx3 = document.getElementById('chart3').getContext('2d');
            var chart3 = new Chart(ctx3, {
                type: 'pie',
                data: {
                    labels: ['Red', 'Blue', 'Yellow'],
                    datasets: [{
                        label: 'Dataset 3',
                        backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)'],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
                        data: [10, 20, 30]
                    }]
                },
                options: {}
            });
        });
    </script>
</body>

</html>