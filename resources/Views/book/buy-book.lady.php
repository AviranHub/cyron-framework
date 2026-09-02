@extends('layouts.master')

@section('keywords', $book->keywords ?? 'کتاب,خرید کتاب,ادبیات,کتاب الکترونیکی')

@section('description',
    $book->meta_description ??
    "خرید کتاب {$book->title} نوشته {$book->author} -
    {$book->introduction}")

@section('content')
    <div class="container mx-auto py-6 px-4">
        <!-- ناوبری -->
        <nav class="flex items-center text-sm mb-6">
            <div class="flex items-center bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm">
                <a href="{{ route('home') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full mr-2">
                        <i class="fa fa-home text-blue-600 text-sm"></i>
                    </div>
                    <span>کلبه کتاب</span>
                </a>
                <i class="fa fa-chevron-left mx-2 text-gray-400 text-xs"></i>
                <a href="{{ route('books') }}" class="text-blue-600 hover:text-blue-800">فروشگاه کتاب</a>
                <i class="fa fa-chevron-left mx-2 text-gray-400 text-xs"></i>
                <span class="text-gray-600 truncate max-w-xs md:max-w-md">خرید کتاب {{ $book->title }}</span>
            </div>
        </nav>

        {{-- @if ($errors->any())
            <div class="containerate mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded dir-rtl">
                    <ul class="list-disc pr-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif --}}

        <!-- بخش اصلی خرید -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="container mx-auto py-8">
                <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">انتخاب روش پرداخت</h1>

                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-book text-blue-500 text-2xl mr-4"></i>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ $book->title }}</h2>
                                <p class="text-gray-600 dark:text-gray-300">نویسنده: {{ $book->author }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">مبلغ قابل پرداخت</h3>
                            <div class="text-3xl font-bold text-green-600 mb-2">{{ number_format($discountedPrice) }} تومان
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">با احتساب تخفیف</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">موجودی کیف پول</h3>
                            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($user->wallet->balance) }} تومان
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">
                                @if ($user->wallet->balance >= $discountedPrice)
                                    <span class="text-green-600">موجودی کافی است</span>
                                @else
                                    <span class="text-red-600">موجودی ناکافی</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">روش پرداخت را انتخاب کنید:</h3>

                        <form action="{{ route('book.purchase', $book) }}" method="POST">
                            @csrf
                            <!-- گزینه 1: پرداخت کامل از طریق درگاه -->
                            <div class="flex items-start mb-4">
                                <input type="radio" id="method_gateway" name="method" value="gateway" class="mt-1 ml-2 mr-3"
                                    checked>
                                <label for="method_gateway" class="flex-1">
                                    <div
                                        class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-800 dark:text-white">پرداخت کامل از طریق درگاه
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">کل مبلغ از طریق درگاه
                                            پرداخت می‌شود.</p>
                                    </div>
                                </label>
                            </div>

                            <!-- گزینه 2: پرداخت کامل از طریق کیف پول -->
                            <div class="flex items-start mb-4">
                                <input type="radio" id="method_wallet" name="method" value="wallet" class="mt-1 ml-2 mr-3"
                                    {{ $user->wallet->balance < $discountedPrice ? 'disabled' : '' }}>
                                <label for="method_wallet" class="flex-1">
                                    <div
                                        class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg p-4 {{ $user->wallet->balance < $discountedPrice ? 'opacity-50' : '' }}">
                                        <h4 class="font-semibold text-gray-800 dark:text-white">پرداخت کامل از طریق کیف پول
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">کل مبلغ از موجودی کیف پول
                                            کسر می‌شود.</p>
                                        @if ($user->wallet->balance < $discountedPrice)
                                            <p class="text-red-500 text-sm mt-2">موجودی کیف پول شما کافی نیست.</p>
                                        @endif
                                    </div>
                                </label>
                            </div>

                            <!-- گزینه 3: پرداخت ترکیبی -->
                            <div class="flex items-start mb-4">
                                <input type="radio" id="method_hybrid" name="method" value="hybrid" class="mt-1 ml-2 mr-3">
                                <label for="method_hybrid" class="flex-1">
                                    <div
                                        class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-800 dark:text-white">پرداخت ترکیبی</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">بخشی از مبلغ از کیف پول و
                                            مابقی از طریق درگاه پرداخت می‌شود.</p>

                                        <div class="mt-3 grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-gray-700 dark:text-gray-300 text-sm mb-1">مبلغ از
                                                    کیف پول (تومان)</label>
                                                <input type="number" name="wallet_amount" id="wallet_amount"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                                    placeholder="0" min="0"
                                                    max="{{ min($user->wallet->balance, $discountedPrice) }}" value="0">
                                            </div>
                                            <div>
                                                <label class="block text-gray-700 dark:text-gray-300 text-sm mb-1">مبلغ از
                                                    درگاه (تومان)</label>
                                                <input type="text" id="gateway_amount" value="{{ $discountedPrice }}"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                                    readonly>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-2">حداکثر مبلغ قابل پرداخت از کیف پول:
                                            {{ number_format(min($user->wallet->balance, $discountedPrice)) }} تومان</p>
                                    </div>
                                </label>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                                    پرداخت
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>


    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // داده‌ها را از data attributes می‌خوانیم
            const walletElement = document.getElementById('wallet_amount');
            const gatewayElement = document.getElementById('gateway_amount');
            
            // مقادیر از data attributes خوانده می‌شوند
            const totalPrice = parseInt("{{ $discountedPrice }}");
            const userWallet = parseInt("{{ $user->wallet }}");
            const maxWalletAmount = Math.min(userWallet, totalPrice);

            // مقدار اولیه
            gatewayElement.value = totalPrice;

            walletElement.addEventListener('input', function() {
                let walletAmount = parseInt(this.value) || 0;
                
                // بررسی مقادیر منفی
                if (walletAmount < 0) {
                    walletAmount = 0;
                    this.value = 0;
                }
                
                // بررسی بیش از حد مجاز
                if (walletAmount > maxWalletAmount) {
                    walletAmount = maxWalletAmount;
                    this.value = maxWalletAmount;
                }
                
                // بررسی بیش از حد کل مبلغ
                if (walletAmount > totalPrice) {
                    walletAmount = totalPrice;
                    this.value = totalPrice;
                }
                
                // محاسبه مبلغ درگاه
                const gatewayAmount = totalPrice - walletAmount;
                gatewayElement.value = gatewayAmount;
            });

            // اضافه کردن event listener برای radio buttons
            const methodRadios = document.querySelectorAll('input[name="method"]');
            methodRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'hybrid') {
                        walletElement.disabled = false;
                        walletElement.value = 0;
                        gatewayElement.value = totalPrice;
                    } else {
                        walletElement.disabled = true;
                        walletElement.value = 0;
                        gatewayElement.value = totalPrice;
                    }
                });
            });

            // غیرفعال کردن input کیف پول در ابتدا
            walletElement.disabled = true;
        });
    </script>
@endsection

@section('styles')
    <style>
        .prose {
            line-height: 1.8;
            font-size: 1.05rem;
        }

        .prose h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 1.5rem 0 1rem;
            color: #2d3748;
        }

        .prose p {
            margin-bottom: 1.2rem;
        }

        .dark .prose h3 {
            color: #e2e8f0;
        }
    </style>
@endsection
