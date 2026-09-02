@extends('Layouts.app')
@section('content')


<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto p-4">

        <!-- Breadcrumbs -->
        <nav class="flex mb-4 bg-white p-3 rounded-lg shadow" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="@route('index')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home ml-2"></i>
                        خانه
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                        <a href="" class="text-sm font-medium text-gray-700 hover:text-blue-600">پیشنهادات</a>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($suggestions as $suggestion)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="relative">
                    <img alt="تصویر صنف ۱" class="w-full h-48 object-cover" src="@storage('{{$suggestion->image}}')" />
                    <div class="absolute top-2 left-2 flex space-x-2">
                        <button class="bg-white p-2 rounded-full shadow-md">
                            <i class="fas fa-heart text-gray-600 text-xl"></i>
                        </button>
                        <button class="bg-white p-2 rounded-full shadow-md">
                            <i class="fas fa-share-alt text-gray-600 text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <h2 class="text-xl font-bold">{{$suggestion->name}}</h2>
                    <div class="mt-4">
                        <div class="flex items-center text-lg text-gray-700">
                            <i class="fas fa-user text-gray-500 ml-2 text-2xl"></i>
                            <span>مدیر صنف: {{$suggestion->manage}}</span>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-lg text-gray-700">
                                <i class="fas fa-map-marker-alt text-gray-500 ml-2 text-2xl"></i>
                                <span>آدرس: {{ $suggestion->address }}</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-lg text-gray-700">
                                <i class="fas fa-tags text-gray-500 ml-2 text-2xl"></i>
                                @php

                                $categoriesArray = json_decode(json_encode($categories), true); // تبدیل به آرایه
                                $id = $suggestion->category; // فرض بر این است که این یک کلید معتبر است
                                $categoryName = null; // مقدار پیش‌فرض

                                // جستجو در آرایه برای پیدا کردن نام دسته با id مشخص
                                foreach ($categoriesArray as $category) {
                                if (isset($category['id']) && $category['id'] == $id) {
                                $categoryName = $category['name']; // نام دسته را ذخیره کن
                                break; // اگر پیدا شد، از حلقه خارج شو
                                }
                                }

                                @endphp
                                <span>دسته بندی: {{ $categoryName }}</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-lg text-gray-700">
                                <i class="fab fa-instagram text-gray-500 ml-2 text-2xl"></i>
                                <span>آیدی اینستاگرام: {{ $suggestion->insta }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</body>
@endsection