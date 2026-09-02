@extends('layouts.master')

@section('content')
    <style>
        body {
            font-family: 'Vazir', sans-serif;
            direction: rtl;
        }

        .x-slider {
            overflow-x: auto;
            white-space: nowrap;
        }

        .x-slide {
            display: inline-flex;
        }

        .x-carusel {
            display: inline-block;
            margin: 0 10px;
        }

        .book-cover {
            width: 150px;
            height: 200px;
            object-fit: cover;
        }
    </style>
    <style>
        @media (min-width: 768px) {
            .bookbox div:nth-child(n+6) {
                display: none;
            }
        }
    </style>
    <style>
        /* CSS سفارشی برای نوار اسکرول */
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
            /* ارتفاع نوار اسکرول */
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background-color: #4b5563;
            /* رنگ نوار اسکرول */
            border-radius: 10px;
            /* گرد کردن گوشه‌ها */
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #0ffffff;
            /* رنگ پس‌زمینه نوار اسکرول */
        }
    </style>
    <style>
        /* استایل‌های جدید برای بخش هیرو */
        .hero-section {
            position: relative;
            overflow: hidden;
            padding: 5rem 0;
            /* background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); */
            /* color: white; */
            border-radius: 0 0 30% 30% / 10%;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E"); */
            opacity: 0.3;
        }

        .hero-books-carousel {
            position: relative;
            perspective: 1000px;
            height: 300px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .carousel-book {
            position: absolute;
            top: 0;
            left: 50%;
            transform-origin: center;
            transition: transform 0.8s cubic-bezier(0.17, 0.67, 0.83, 0.67),
                width 0.8s ease,
                height 0.8s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.3);
            background: #fff;
            z-index: 3;
        }

        .carousel-book img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* موقعیت‌های مختلف کتاب‌ها */
        .book-left {
            transform: translateX(-150%) rotateY(-20deg);
            width: 180px;
            height: 270px;
            z-index: 3;
        }

        .book-center {
            transform: translateX(-50%) rotateY(0deg);
            width: 200px;
            height: 300px;
            z-index: 4;
            animation: float 4s ease-in-out infinite;
        }

        .book-right {
            transform: translateX(50%) rotateY(20deg);
            width: 180px;
            height: 270px;
            z-index: 3;
        }

        @keyframes float {
            0% {
                transform: translateX(-50%) rotateY(0deg) translateY(0);
            }

            50% {
                transform: translateX(-50%) rotateY(0deg) translateY(-20px);
            }

            100% {
                transform: translateX(-50%) rotateY(0deg) translateY(0);
            }
        }

        .cta-button {
            /* background: linear-gradient(45deg, #6df951, #51f95f); */
            /* color: #1e3c72; */
            font-weight: bold;
            padding: 12px 30px;
            border-radius: 50px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            display: inline-block;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            .hero-books-carousel {
                height: 250px;
            }

            .book-left,
            .book-right {
                width: 120px;
                height: 180px;
            }

            .book-center {
                width: 140px;
                height: 210px;
            }
        }
    </style>
    <section class="hero-section dark:text-white">
        <div class="hero-bg-pattern"></div>
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div class="hero-content lg:w-5/6 mb-10 lg:mb-0">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight">
                        دنیای کتاب‌ها را در <span class="text-amber-500 dark:text-amber-300">کلبه کتاب</span> کشف کنید
                    </h1>
                    <p class="text-xl mb-8 opacity-90">
                        بهترین کتاب‌های الکترونیک را بخوانید، دانلود کنید و لذت ببرید. عضویت رایگان!
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}"
                            class="cta-button bg-gradient-to-r from-amber-400 to-amber-500 text-black text-center">
                            همین حالا شروع کنید
                            <i class="fas fa-arrow-left mr-2"></i>
                        </a>
                        <a href="{{ route('books') }}"
                            class="bg-white text-gray-800 px-6 py-3 rounded-full shadow-lg font-bold hover:bg-gray-200 hover:scale-105 transition duration-300 text-center">
                            مشاهده کتاب‌ها
                        </a>
                    </div>
                </div>

                <div class="hero-books-carousel lg:w-1/6">
                    @foreach ($low_books->take(3) as $index => $low_book)
                        @if ($index === 0)
                            <div class="carousel-book book-left">
                            @elseif($index === 1)
                                <div class="carousel-book book-center">
                                @elseif($index === 2)
                                    <div class="carousel-book book-right">
                        @endif
                        <a href="{{ route('book', ['slug' => $low_book->slug]) }}">
                            <img src="{{ asset('/storage/book/' . $low_book->cover) }}" loading="lazy"
                                alt="{{ $low_book->title }}">
                        </a>
                </div>
                @endforeach
            </div>
        </div>
        </div>
    </section>


    <x-book-slider title="جدیدترین‌ها" :targetId="'scroll-container-1'" :seeAllUrl="route('category.books', ['category' => 'newest'])">
        @foreach ($newestBooks as $book)
            <div class="w-40 md:w-48">
                <a class="" href="{{ route('book', ['slug' => $book->slug]) }}" title="{{ $book->title }}"
                    aria-label="{{ $book->title }}">
                    <img alt="{{ $book->title }}" class="rounded-lg mb-2 w-full h-56 md:h-64" height="200"
                        src="{{ asset('/storage/book/' . $book->cover) }}" loading="lazy" width="150" />
                    <div class="text-center mb-2">
                        <p class="text-lg font-bold m-0 dark:text-gray-400 break-words overflow-hidden truncate max-w-xs">
                            {{ $book->title }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-500 m-0">
                            نویسنده: {{ $book->author_name }}
                        </p>
                        @if ($book->copen == 100 or $book->price == 0)
                            <p class="text-sm text-gray-600 dark:text-gray-500 m-0"><span
                                    class="text-green-600">رایگان</span></p>
                        @else
                            <p class="text-sm text-gray-600 dark:text-gray-500 m-0"><span
                                    class="text-green-600">{{ number_format($book->price) }}</span> تومان</p>
                        @endif
                    </div>
                </a>
            </div>
        @endforeach
    </x-book-slider>

    <x-book-slider title="کتاب‌های رایگان" :targetId="'scroll-container-2'" :seeAllUrl="route('category.books', ['category' => 'free'])">
        @foreach ($freeBooks as $book)
            <div class="w-40 md:w-48">
                <a class="" href="{{ route('book', ['slug' => $book->slug]) }}" title="{{ $book->title }}"
                    aria-label="{{ $book->title }}">
                    <img alt="{{ $book->title }}" class="rounded-lg mb-2 w-full h-56 md:h-64" height="200"
                        src="{{ asset('/storage/book/' . $book->cover) }}" loading="lazy" width="150" />
                    <div class="text-center mb-2">
                        <p class="text-lg font-bold m-0 dark:text-gray-400 break-words overflow-hidden truncate max-w-xs">
                            {{ $book->title }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-500 m-0">
                            نویسنده: {{ $book->author_name }}
                        </p>

                        @if ($book->copen == 100 or $book->price == 0)
                            <p class="text-sm text-gray-600 dark:text-gray-500 m-0"><span
                                    class="text-green-600">رایگان</span></p>
                        @else
                            <p class="text-sm text-gray-600 dark:text-gray-500 m-0"><span
                                    class="text-green-600">{{ number_format($book->price) }}</span> تومان</p>
                        @endif
                    </div>
                </a>
            </div>
        @endforeach
    </x-book-slider>








    <div class="container mx-auto py-4">
        @foreach ($categories as $category)
            <x-book-slider title="{{ $category->name }}" :targetId="'scroll-container-2'" :seeAllUrl="route('category.books', ['category' => $category->slug])">
                @foreach ($category->books as $book)
                    <div class="w-40 md:w-48">
                        <a class="" href="{{ route('book', ['slug' => $book->slug]) }}" title="{{ $book->title }}"
                            aria-label="{{ $book->title }}">
                            <img alt="{{ $book->title }}" class="mb-2 w-full rounded-lg h-56 md:h-64" height="200"
                                src="{{ asset('/storage/book/' . $book->cover) }}" loading="lazy" width="150" />
                            <div class="text-center mb-2">
                                <p
                                    class="text-lg font-bold m-0 dark:text-gray-400 break-words overflow-hidden truncate max-w-xs">
                                    {{ $book->title }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-500 m-0">
                                    نویسنده: {{ $book->author_name }}
                                </p>

                                @if ($book->copen == 100 or $book->price == 0)
                                    <p class="text-sm text-gray-600 dark:text-gray-500 m-0"><span
                                            class="text-green-600">رایگان</span></p>
                                @else
                                    <p class="text-sm text-gray-600 dark:text-gray-500 m-0"><span
                                            class="text-green-600">{{ number_format($book->price) }}</span> تومان</p>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </x-book-slider>
        @endforeach
    </div>


    {{-- <div class="container mx-auto py-4">
        @foreach ($categories as $category)
            <div class="bookbox mb-8">
                <div class="grid grid-cols-2 gap-4 items-center mb-4">
                    <div class="text-right">
                        <div class="text-xl font-bold dark:text-gray-300">
                            {{ $category->name }}
                        </div>
                    </div>
                    <div class="text-left">
                        <a class="text-blue-500" href="{{ route('category.books', ['category' => $category->slug]) }}">
                            مشاهده همه
                        </a>
                    </div>
                </div>
                <div
                    class="flex overflow-x-auto space-x-2 gap-3 overflow-y-hidden md:grid md:grid-cols-5 md:place-content-center md:gap-2 md:space-x-0">
                    @foreach ($category->books as $book)
                        <div class="w-40 md:w-48 min-w-[160px] md:min-w-0">
                            <a class="" href="{{ route('book', ['slug' => $book->slug]) }}"
                                title="مشاهده جزئیات کتاب {{ $book->title }}"
                                aria-label="مشاهده جزئیات کتاب {{ $book->title }}">
                                <img alt="جلد کتاب {{ $book->title }}" class="rounded-lg mb-2 w-full h-56 md:h-64"
                                    loading="lazy" src="{{ asset('/storage/book/' . $book->cover) }}" />
                                <div class="text-center mb-2">
                                    <p
                                        class="text-lg font-bold m-0 dark:text-gray-400 break-words overflow-hidden truncate max-w-xs">
                                        {{ $book->title }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-500 m-0">
                                        نویسنده: {{ $book->author_name }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div> --}}




    {{-- 
    <div class="container mx-auto py-4">
        @foreach ($categories as $category)
            <div class="bookbox mb-8">
                <div class="grid grid-cols-2 gap-4 items-center mb-4">
                    <div class="text-right">
                        <div class="text-xl font-bold dark:text-gray-300">
                            {{ $category->name }}
                        </div>
                    </div>
                    <div class="text-left">
                        <a class="text-blue-500" href="{{ route('category.books', ['category' => $category->slug]) }}">
                            مشاهده همه
                        </a>
                    </div>
                </div>
                <div class="flex justify-between">
                    <div class="flex space-x-2 overflow-x-auto overflow-y-hidden"
                        id="scroll-container-{{ $loop->index }}">
                        <div class="flex x-slide gap-4 whitespace-nowrap justify-center">
                            @foreach ($category->books as $book)
                                <div class="w-40 md:w-48">
                                    <a class="" href="{{ route('book', ['slug' => $book->slug]) }}"
                                        title="مشاهده جزئیات کتاب اول" aria-label="مشاهده جزئیات کتاب اول">
                                        <img alt="جلد کتاب اول" class="rounded-lg mb-2 w-full h-56 md:h-64"
                                            height="200" src="{{ asset('/storage/book/' . $book->cover) }}"
                                            loading="lazy" width="150" />
                                        <div class="text-center mb-2">
                                            <p
                                                class="text-lg font-bold m-0 dark:text-gray-400 break-words overflow-hidden truncate max-w-xs">
                                                {{ $book->title }}
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-500 m-0">
                                                نویسنده: {{ $book->author_name }}
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.querySelector('.hero-books-carousel');
            const books = carousel.querySelectorAll('.carousel-book');

            // چرخش کاروسل کتاب‌ها هر 5 ثانیه
            setInterval(rotateCarousel, 5000);

            function rotateCarousel() {
                // تغییر کلاس‌ها برای ایجاد افکت چرخش
                books.forEach(book => {
                    if (book.classList.contains('book-left')) {
                        book.classList.remove('book-left');
                        book.classList.add('book-center');
                    } else if (book.classList.contains('book-center')) {
                        book.classList.remove('book-center');
                        book.classList.add('book-right');
                    } else if (book.classList.contains('book-right')) {
                        book.classList.remove('book-right');
                        book.classList.add('book-left');
                    }
                });
            }
        });
    </script>
    <script>
        document.querySelectorAll('.scroll-left').forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const scrollContainer = document.getElementById(targetId);

                scrollContainer.scrollBy({
                    top: 0,
                    left: -320,
                    behavior: 'smooth'
                });
            });
        });

        document.querySelectorAll('.scroll-right').forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const scrollContainer = document.getElementById(targetId);

                scrollContainer.scrollBy({
                    top: 0,
                    left: 320,
                    behavior: 'smooth'
                });
            });
        });
    </script>
@endsection
