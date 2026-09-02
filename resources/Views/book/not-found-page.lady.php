@extends('layouts.master')


@section('keywords')

@endsection

@section('description')
{{ $book->title }}
{{ $book->introduction }}
@endsection



@section('content')

<div class="container mt-6 py-6">
    <div class="flex items-center bg-white dark:bg-zinc-900 p-4 rounded-lg shadow-md">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-10 h-10 ml-2 bg-green-100 dark:bg-green-900 rounded-full mr-2">
                <i class="fa fa-home text-green-600 dark:text-green-300"></i>
            </div>
            <a href="#" class="text-green-600 hover:underline">کلبه کتاب</a>
            <i class="fa fa-chevron-left mx-2 text-gray-400"></i>
            <span class="text-gray-600">بلاگ</span>
        </div>
    </div>
</div>

<div class="flex flex-col gap-4">

    <div class="container">
        <div class="flex flex-col md:flex-row p-2 dir-rtl bg-white dark:bg-gray-900 dark:text-gray-300 shadow-lg rounded-lg">
            <div class="book-cover-box md:w-1/3">
                <img src="{{ storage_url('/book/'.$book->cover) }}" alt="{{ $book->title }}" loading="lazy" class="w-full h-48 rounded-lg">
            </div>
            <div class="flex flex-col p-4 md:w-2/3">
                <h1 class="font-yekan text-2xl mb-2">{{ $book->title }}</h1>
                <p class="mb-1">نویسنده: 
                    @if($book->author_id == "author")
                    <a href="{{ route('author',['id' => $book->publisher_id]) }}" class="text-green-600 hover:underline">{{ $book->author }}</a>
                    @else
                    <span class="text-green">{{ $book->author_name }}</span>
                    @endif
                </p>
                @if($book->genre)
                <p class="mb-1">ژانر: <span class="text-green">{{ $book->genre->name }}</span></p>
                @endif
                @if($book->copen > 0)
                <p class="mb-1">تخفیف: 
                    <span class="bg-red-500 text-white rounded px-2 py-1">{{ $book->copen }}%</span>
                </p>
                @endif
                @if($book->copen == 100 || $book->price == 0)
                <p class="mb-1">قیمت: <span class="text-green">رایگان</span></p>
                @else
                <p class="mb-1">قیمت: <span class="text-green">{{ $book->price }}</span> تومان</p>
                @endif
            </div>
        </div>
    </div>

    <div class="container">
        <div class="text-black dark:text-white bg-white dark:bg-zinc-800 m-0-5 dir-rtl font-vazir rounded-lg shadow-lg p-4 text-center">
            <p class="font-yekan text-xl">این صفحه از کتاب پیدا نشد</p>
        </div>
    </div>

    

</div>

@endsection
