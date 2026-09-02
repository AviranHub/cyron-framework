@extends('auth.layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <!-- عنوان -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">✨ عضویت در کلبه کتاب</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">برای شروع، اطلاعات خود را وارد کنید</p>
        </div>

        <!-- کارت ثبت‌نام -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-100 dark:border-gray-700">
            
            <!-- نمایش پیام‌های خطا -->
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
                برای ثبت‌نام، حداقل یکی از فیلدهای <strong>ایمیل</strong> یا <strong>تلفن</strong> را پر کنید.
            </div>

            <!-- فرم ثبت‌نام -->
            <form method="POST" action="{{ route('register') }}" x-data="{ showPassword: false, showPasswordConfirmation: false }">
                @csrf

                <!-- نام کامل -->
                <div class="mb-5">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">
                        نام کامل <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full pl-4 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition duration-200"
                               placeholder="نام و نام خانوادگی" required>
                    </div>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ایمیل (غیراجباری) -->
                <div class="mb-5">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">
                        ایمیل <span class="text-gray-400 text-xs">(اختیاری)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full pl-4 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition duration-200"
                               placeholder="example@email.com">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- تلفن (غیراجباری) -->
                <div class="mb-5">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">
                        شماره تلفن <span class="text-gray-400 text-xs">(اختیاری)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <i class="fas fa-phone"></i>
                        </span>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               class="w-full pl-4 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition duration-200"
                               placeholder="0912xxx">
                    </div>
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- رمز عبور -->
                <div class="mb-5">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">
                        رمز عبور <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input :type="showPassword ? 'text' : 'password'" name="password"
                               class="w-full pl-12 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition duration-200"
                               placeholder="••••••••" required>
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                                aria-label="نمایش رمز عبور">
                            <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- تکرار رمز -->
                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">
                        تکرار رمز عبور <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <input :type="showPasswordConfirmation ? 'text' : 'password'" name="password_confirmation"
                               class="w-full pl-12 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition duration-200"
                               placeholder="••••••••" required>
                        <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation"
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                                aria-label="نمایش تکرار رمز عبور">
                            <i class="fas" :class="showPasswordConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- دکمه ثبت‌نام -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-user-plus ml-2"></i>
                    ثبت‌نام
                </button>
            </form>

            <!-- خط جداکننده و دکمه‌های اجتماعی -->
            <div class="flex items-center my-6">
                <div class="flex-1 border-t border-gray-300 dark:border-gray-600"></div>
                <span class="px-4 text-sm text-gray-500 dark:text-gray-400">یا</span>
                <div class="flex-1 border-t border-gray-300 dark:border-gray-600"></div>
            </div>

            <div class="space-y-3">
                <a href="{{ route('auth.google') }}"
                   class="w-full flex items-center justify-center gap-3 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-medium py-3 px-4 rounded-lg transition duration-200 shadow-sm hover:shadow">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#EA4335" d="M5.266 9.765A7.077 7.077 0 0 1 12 4.909c1.69 0 3.218.6 4.418 1.582l3.283-3.283C17.334 1.295 14.835 0 12 0 7.31 0 3.254 2.698 1.282 6.57l3.984 3.195z"/>
                        <path fill="#34A853" d="M23.454 12.27c0-.806-.073-1.582-.209-2.327H12v4.455h6.455a5.447 5.447 0 0 1-2.309 3.567l3.582 3.582c2.091-1.927 3.726-4.764 3.726-8.277z"/>
                        <path fill="#4A90E2" d="M5.62 14.19a7.09 7.09 0 0 1-.364-2.19c0-.755.128-1.482.364-2.19L1.282 6.57A12.04 12.04 0 0 0 0 12c0 1.934.451 3.759 1.282 5.43l4.338-3.24z"/>
                        <path fill="#FBBC05" d="M12 24c3.24 0 5.955-1.073 7.927-2.873l-3.582-3.582c-1.018.691-2.318 1.109-3.745 1.109-2.655 0-4.91-1.745-5.709-4.164l-3.984 3.195C3.255 21.302 7.31 24 12 24z"/>
                    </svg>
                    ثبت‌نام با گوگل
                </a>
            </div>

            <!-- لینک بازگشت -->
            <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                قبلاً ثبت‌نام کردید؟
                <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 font-semibold transition">
                    وارد شوید
                </a>
            </div>
        </div>
    </div>
</div>
@endsection