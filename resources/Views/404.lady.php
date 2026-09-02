@extends('Layouts.app')

@section('style')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;700&display=swap');
        body {
            font-family: 'Vazirmatn', sans-serif;
        }
    </style>
@endsection

@section('content')
<div class="bg-white flex items-center justify-center min-h-screen">
    <div class="text-center">
        <div class="text-6xl font-bold mb-4 flex justify-center items-center">
            <span>4</span>
            <span class="relative mx-2">
                <span class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-search text-4xl"></i>
                </span>
                <span class="opacity-0">0</span>
            </span>
            <span>4</span>
        </div>
        <p class="text-gray-600 mb-4">متاسفانه صفحه مورد نظر شما وجود ندارد.</p>
        <p class="text-gray-600 mb-4">
            اما می‌توانید هر نوعی از اقامتگاه را در 
            <a href="#" class="text-orange-500 underline">@var('APP_NAME') پیدا کنید</a>
        </p>
        <button class="bg-gray-800 text-white py-2 px-4 rounded">
            رفتن به صفحه‌ی اصلی @var('APP_NAME')
        </button>
    </div>
</div>
@endsection
