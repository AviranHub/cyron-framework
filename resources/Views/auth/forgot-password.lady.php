@extends('auth.layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <!-- عنوان -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">🔑 فراموشی رمز عبور</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">
                ایمیل یا شماره تلفن خود را وارد کنید تا لینک بازیابی برای شما ارسال شود
            </p>
        </div>

        <!-- کارت -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-100 dark:border-gray-700">
            
            <!-- پیام‌های خطا/موفقیت -->
            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/30 border-r-4 border-red-500 p-4 mb-6 rounded-lg">
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/30 border-r-4 border-green-500 p-4 mb-6 rounded-lg">
                    <p class="text-green-600 dark:text-green-400 text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <!-- پیام توضیحی -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-6 text-sm text-blue-700 dark:text-blue-300">
                <i class="fas fa-info-circle ml-2"></i>
                لینک یا کد بازیابی به <strong>ایمیل</strong> یا <strong>تلفن</strong> شما ارسال خواهد شد.
            </div>

            <!-- فرم -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- فیلد ایمیل/تلفن -->
                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">
                        ایمیل یا شماره تلفن
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="text" name="email" value="{{ old('email') }}"
                               class="w-full pl-4 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200"
                               placeholder="example@email.com یا 0912xxx" required>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- دکمه ارسال -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-refresh ml-2"></i>
                    بازیابی رمز عبور
                </button>
            </form>

            <!-- لینک بازگشت -->
            <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-semibold transition">
                    <i class="fas fa-arrow-right ml-1"></i>
                    بازگشت به صفحه ورود
                </a>
            </div>
        </div>
    </div>
</div>
@endsection