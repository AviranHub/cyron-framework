@extends('layouts.master')


@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <h1 class="text-8xl font-bold text-red-500">500</h1>
    <div class="text-6xl mb-4 mt-4">⚠️</div>
    <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-2">خطای داخلی سرور!</h2>
    <p class="text-gray-500 dark:text-gray-400 mb-6">
        مشکلی در سرور رخ داده است. لطفاً بعداً تلاش کنید.
    </p>
    <div class="flex gap-4">
        <a href="/" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-home ml-2"></i> صفحه اصلی
        </a>
        <button onclick="location.reload()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-sync-alt ml-2"></i> تلاش مجدد
        </button>
    </div>
</div>
@endsection