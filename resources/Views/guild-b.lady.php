@extends('Layouts.app')
@section('content')
<style>
    body {
        font-family: 'Vazirmatn', sans-serif;
    }
</style>
@endsection

@section('content')
<div class="bg-gray-100">

    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden mt-6">
        <div class="relative">
            <img alt="کلبه جنگلی در منطقه سرسبز" class="w-full h-48 object-cover" height="200" src="https://storage.googleapis.com/a1aa/image/Hmjfic6KL7VCC606sNzZuCvVrSxO1crvXEJecOCxjyZM8mlTA.jpg" width="400" />
            <div class="absolute top-2 left-2 flex space-x-2">
                <button class="bg-white p-2 rounded-full shadow-md">
                    <i class="fas fa-heart text-gray-600">
                    </i>
                </button>
                <button class="bg-white p-2 rounded-full shadow-md">
                    <i class="fas fa-share-alt text-gray-600">
                    </i>
                </button>
            </div>
        </div>
        <div class="p-4 pb-20">
            <h2 class="text-lg font-bold">
                اجاره کلبه فلت جنگلی مانگنیسو ماسال
            </h2>
            <div class="flex items-center text-sm text-gray-500 mt-1">
                <i class="fas fa-star text-yellow-500">
                </i>
                <span class="ml-1">
                    ۴.۷ (۲۳ نظر ثبت شده)
                </span>
                <span class="mx-2">
                    ·
                </span>
                <span>
                    استان گیلان، ماسال
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-sm font-bold">
                    کلبه
                </h3>
                <p class="text-sm text-gray-600">
                    اجاره کلبه در ماسال به میزبان رقیه حسینی
                </p>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-comments text-gray-500">
                    </i>
                    <span class="ml-2">
                        چت با میزبان
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    امکان چت آنلاین بعد از ثبت رزرو وجود دارد.
                </p>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-car text-gray-500">
                    </i>
                    <span class="ml-2">
                        رزرو آنی و قطعی جاپاما
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    در صورت رزرو این اقامتگاه، رزرو شما قطعی است و نیازی به تایید میزبان نیست.
                </p>
            </div>
            <div class="mt-4 bg-yellow-100 p-2 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle text-yellow-500">
                </i>
                <span class="ml-2 text-sm text-yellow-700">
                    نیاز به مشاوره رزرو دارید؟
                </span>
                <p class="text-xs text-gray-500 mt-1">
                    امکان چت آنلاین بعد از ثبت رایگان رزرو
                </p>
            </div>
            <div class="mt-2 text-center text-sm text-gray-500">
                <span>
                    شروع از ۵۰۰,۰۰۰ تومان / شب
                </span>
            </div>
        </div>
    </div>
    <div class="fixed bottom-0 left-0 w-full bg-white p-4 shadow-md">
        <div class="max-w-md mx-auto">
            <button class="w-full bg-black text-white py-2 rounded-lg">
                انتخاب تاریخ
            </button>
        </div>
    </div>
</div>
@endsection