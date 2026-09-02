@extends('layouts.master')

@section('content')
<div class="container mx-auto mt-6 py-6">
    <div class="flex items-center bg-white dark:bg-zinc-900 p-4 rounded-lg shadow-md font-irsans">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-10 h-10 ml-2 bg-green-100 dark:bg-green-900 rounded-full mr-2">
                <i class="fa fa-home text-green-600 dark:text-green-300"></i>
            </div>
            <a href="{{ route('home') }}" class="text-green-600 hover:underline">کلبه کتاب</a>
            <i class="fa fa-chevron-left mx-2 text-gray-400"></i>
            @isset($category)
            <a href="{{ route('books') }}" class="text-green-600 hover:underline">کتاب ها</a>
            <i class="fa fa-chevron-left mx-2 text-gray-400"></i>
            <span class="text-gray-600">{{ $category }}</span>
            @else
            <span class="text-gray-600">کتاب ها</span>
            @endisset
        </div>
    </div>
</div>

<div class="container mx-auto">

    <h1 class="text-3xl dark:text-gray-200 py-4 font-vazir">
        @isset($category)
        دسته بندی: {{ $category }}
        @else
        کتاب ها
        @endisset
    </h1>

    {{-- <div class="flex flex-wrap py-2 gap-10 lg:gap-4">
        @foreach ($books->items() as $book)
        <div class="flex flex-col">
            <div class="w-40 md:w-48">
                <a href="{{ route('book', ['slug' => $book->slug]) }}" aria-label="{{ $book->title }}">
    <div class="text-gray-800 dark:text-gray-300 bg-zinc-700 rounded-lg rounded overflow-hidden border border-zinc-600 font-irsans">
        <img class="w-48 h-56 md:h-60" src="{{ storage_url('/book/' . $book->cover) }}"
            alt="{{ $book->title }}">
        <div class="py-2 text-center">
            <h5 class="text-md py-1 pt-2 break-words overflow-hidden truncate max-w-xs">
                {{ $book->title }}
            </h5>
            <p class="text-sm mb-2 text-gray-700 dark:text-gray-400">{{ $book->author_name }}</p>
            @if ($book->copen == 100 or $book->price == 0)
            <p class="text-sm text-gray-600 dark:text-gray-500 m-0"><span class="bg-lime-700 text-white rounded-full px-3 py-1 pb-0">رایگان</span></p>
            @else
            <p class="text-sm text-gray-700 dark:text-gray-400 m-0"><span class="text-lime-600">{{ number_format($book->price) }}</span> تومان</p>
            @endif
        </div>
    </div>
    </a>
</div>
</div>
@endforeach
</div> --}}

{{-- <div class="flex flex-wrap py-2 gap-10 lg:gap-4">
    @foreach ($books->items() as $book)
    <div class="flex flex-col">
        <div class="w-40 md:w-48">
            {{-- اضافه کردن کلاس group به لینک
            <a href="{{ route('book', ['slug' => $book->slug]) }}"
                aria-label="{{ $book->title }}"
                class="group block">
                <div class="relative text-gray-800 dark:text-gray-300 bg-zinc-700 rounded-lg overflow-hidden border border-zinc-600 font-irsans">
                    {{-- اضافه کردن transition و group-hover:scale به تصویر 
                    <div class="absolute top-2 right-2 inline-flex justify-center items-center bg-black/20 rounded-full p-2 z-10"><i class="far fa-bookmark"></i></div>
                    <img class="w-48 h-56 md:h-60 transition-transform duration-300 ease-in-out group-hover:scale-110 will-change-transform"
                        src="{{ storage_url('/book/' . $book->cover) }}"
                        alt="{{ $book->title }}">
                    <div class="py-2 text-center">
                        <h5 class="text-md py-1 pt-2 break-words overflow-hidden truncate max-w-xs">
                            {{ $book->title }}
                        </h5>
                        <p class="text-sm mb-2 text-gray-700 dark:text-gray-400">{{ $book->author_name }}</p>
                        @if ($book->copen == 100 or $book->price == 0)
                        <p class="text-sm text-gray-600 dark:text-gray-500 m-0">
                            <span class="bg-lime-700 text-white rounded-full px-3 py-1 pb-0">رایگان</span>
                        </p>
                        @else
                        <p class="text-sm text-gray-700 dark:text-gray-400 m-0">
                            <span class="text-lime-600">{{ number_format($book->price) }}</span> تومان
                        </p>
                        @endif
                    </div>
                </div>
            </a>
        </div>
    </div>
    @endforeach
</div> --}}

<div class="flex flex-wrap py-2 gap-10 lg:gap-4">
    @foreach ($books->items() as $book)
    <div class="flex flex-col group">
        <div class="w-40 md:w-48 relative">
            {{-- لینک کتاب (کل صفحه به جز بوکمارک) --}}
            <a href="{{ route('book', ['slug' => $book->slug]) }}"
               aria-label="{{ $book->title }}"
               class="block">
                <div class="text-gray-800 dark:text-gray-300 bg-zinc-700 rounded-lg overflow-hidden border border-zinc-600 font-irsans">
                    <img class="w-48 h-56 md:h-60 transition-transform duration-300 ease-in-out group-hover:scale-110 will-change-transform"
                         src="{{ storage_url('/book/' . $book->cover) }}"
                         alt="{{ $book->title }}">
                    <div class="py-2 text-center">
                        <h5 class="text-md py-1 pt-2 break-words overflow-hidden truncate max-w-xs">
                            {{ $book->title }}
                        </h5>
                        <p class="text-sm mb-2 text-gray-700 dark:text-gray-400">{{ $book->author_name }}</p>
                        @if ($book->copen == 100 or $book->price == 0)
                        <p class="text-sm text-gray-600 dark:text-gray-500 m-0">
                            <span class="bg-lime-700 text-white rounded-full px-3 py-1 pb-0">رایگان</span>
                        </p>
                        @else
                        <p class="text-sm text-gray-700 dark:text-gray-400 m-0">
                            <span class="text-lime-600">{{ number_format($book->price) }}</span> تومان
                        </p>
                        @endif
                    </div>
                </div>
            </a>

            {{-- دکمه بوکمارک در بیرون از لینک --}}
            <button type="button"
                    onclick="toggleBookmark({{ $book->id }}, this)"
                    class="absolute top-2 right-2 inline-flex justify-center items-center bg-black/40 hover:bg-black/60 rounded-full px-2.5 py-1 z-10 transition-all duration-200 hover:scale-110">
                <i class="fa-regular fa-bookmark text-white text-lg"></i>
            </button>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination Links -->
<div class="mt-4">
    {!! $books->links('pagination') !!}
</div>
</div>
@endsection