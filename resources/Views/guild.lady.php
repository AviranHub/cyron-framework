@extends('Layouts.app')
@section('content')
<style>
    body {
        font-family: 'Vazirmatn', sans-serif;
    }
</style>
@endsection

@section('content')

@php

$categoriesArray = json_decode(json_encode($categories), true); // تبدیل به آرایه
$id = $guild->category; // فرض بر این است که این یک کلید معتبر است
$categoryName = null; // مقدار پیش‌فرض
$categorySlug = null; // مقدار پیش‌فرض

// جستجو در آرایه برای پیدا کردن نام دسته با id مشخص
foreach ($categoriesArray as $category) {
if (isset($category['id']) && $category['id'] == $id) {
$categoryName = $category['name']; // نام دسته را ذخیره کن
$categorySlug = $category['slug'];
break; // اگر پیدا شد، از حلقه خارج شو
}
}

@endphp

<div class="container mx-auto mt-4">
    <!-- Breadcrumbs -->
    <nav class="flex mb-4 bg-white p-3 rounded-lg shadow" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-home ml-2"></i>
                    خانه
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                    <a href="@route('guilds')" class="text-sm font-medium text-gray-700 hover:text-blue-600">اصناف</a>
                </div>
            </li>
            @isset($categoryName)
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                    <a href="@route('guilds-category',['slug' => '$categorySlug'])" class="text-sm font-medium text-gray-700 hover:text-blue-600">{{$categoryName}}</a>
                </div>
            </li>
            @endisset
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                    <a href="" class="text-sm font-medium text-gray-700 hover:text-blue-600">{{ $guild->name }}</a>
                </div>
            </li>
        </ol>
    </nav>
    <div class="flex flex-wrap -mx-3">
        <div class="w-full lg:w-3/4 xl:w-2/3 px-3 mb-6 mx-auto">
            <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col lg:flex-row h-full">
                <div class="relative lg:w-1/2">
                    <img alt="{{ $guild->name }}" class="w-full h-48 lg:h-full object-cover" src="@storage('{{$guild->image}}')" />
                </div>
                <div class="p-4 flex-grow lg:w-1/2">
                    <h2 class="text-xl font-bold">
                        {{ $guild->name }}
                    </h2>
                    <div class="mt-4">
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-user text-gray-500 mr-2">
                            </i>
                            <span>
                                مدیر صنف: {{ $guild->manage }}
                            </span>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-map-marker-alt text-gray-500 mr-2">
                                </i>
                                <span>
                                    آدرس: {{ $guild->address }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-tags text-gray-500 mr-2">
                                </i>
                                <span>
                                    دسته بندی: {{ $categoryName }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-gray-700">
                                <i class="fab fa-instagram text-gray-500 mr-2">
                                </i>
                                <span>
                                    آیدی اینستاگرام: {{ $guild->insta }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>










@endsection