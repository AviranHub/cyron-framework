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
                <a href="{{ route('home') }}" class="text-green-600 hover:underline">کلبه کتاب</a>
                <i class="fa fa-chevron-left mx-2 text-gray-400"></i>
                <span class="text-gray-600">بلاگ</span>
            </div>
        </div>
    </div>

    <div class="flex lg:flex-row flex-col gap-2 lg:gap-4">
        <div class="container w-full lg:w-1/3">
            <div class="flex flex-col p-2  bg-white dark:bg-gray-700 shadow-lime rounded-xl justify-center">

                <div class="flex flex-col p-2 ">
                    <div class="book-cover-box">
                        <img src="{{ storage_url('/book/' . $book->cover) }}" alt=""
                            class="w-full h-[400px] sm:h-[400px] md:h-[450px] lg:h-[350px] xl:h-[450px] 2xl:h-[550px]">
                    </div>
                    <div class="flex flex-col p-2 font-vazir text-lg">

                        <p class="font-yekan text-2xl">{{ $book->title }}</p>
                        <p class="">نویسنده :
                            @if ($book->author_id == 'author')
                                <a class=""
                                    href="{{ route('author', ['id' => $book->publisher_id]) }}">{{ $book->author_name }}</a>
                            @else
                                <span class="text-green-500">{{ $book->author_name }}</span>
                            @endif
                        </p>

                        @if ($genre)
                            <p class="">ژانر : <span class="text-green-500">{{ $genre->name }}</span></p>
                        @endif





                        @if ($book->copen > 0)
                            <p>تخفیف :
                                <span
                                    class="bg-red-500 text-white rounded-lg padding-01-05 text-sm">{{ $book->copen }}%</span>
                            </p>
                        @endif
                        @if ($book->copen == 100 or $book->price == 0)
                            <p class="">قیمت : <span class="text-green-500">رایگان</span></p>
                        @else
                            <p class="">قیمت : <span class="text-green-500">{{ $book->price }}</span> تومان</p>
                        @endif

                    </div>
                </div>

                <div class="inline-flex flex-col items-center justify-center p-1 font-vazir fs-18 gap-2">

                    {{-- <a href="{{ route('book.buy', ['slug' => $book->slug]) }}" class="w-48 text-green-500 rounded-lg border-2 border-green-500 text-decoration-none text-center py-2 px-6">خرید نسخه کامل</a> --}}

                    @if ($book->copen == 100 or $book->price == 0)
                        <a href="{{ route('add-to-library', ['slug' => $book->slug]) }}"
                            class="w-48 text-green-500 rounded-lg border-2 border-green-500 text-decoration-none text-center py-2 px-6">
                            افزودن به کتابخانه
                        </a>
                    @else
                        <a href="{{ route('book.buy', ['slug' => $book->slug]) }}"
                            class="w-48 text-green-500 rounded-lg border-2 border-green-500 text-decoration-none text-center py-2 px-6">خرید
                            نسخه کامل</a>
                    @endif
                </div>

            </div>
        </div>




        <div class="container w-full lg:w-2/3">
            <div
                class="bg-gray-300 dark:bg-gray-700 dark:text-gray-300 m-0-5  font-vazir rounded-xl shadow-lime p-2 position-relative w-a90-d70">

                <p class="font-vazir text-green-500 text-2xl text-right m-0-5 font-vazir br10 shadow-lime p-2">
                    {{ $book->title }}</p>
                <div class="text-lg text-right m-0-5 font-faraz br10 shadow-lime p-2">
                    <pre class="font-irsans py-4 whitespace-pre-wrap">{!! $bookPart->text !!}</pre>
                </div>


                <nav aria-label="Page navigation example">
                    @php
                        $nextPage = $pageNumber + 1;
                        $prevPage = $pageNumber - 1;
                    @endphp
                    <ul class="flex items-center -space-x-px h-10 text-base">
                        <li>
                            <a href="{{ $prevPage > 0 ? route('bookpage-fv', ['slug' => $book->slug, 'id' => $prevPage]) : '#' }}"
                                class="flex items-center justify-center px-4 h-10 ms-0 leading-tight {{ $prevPage > 0 ? 'text-gray-500 bg-gray-800 hover:bg-gray-700 hover:text-white' : 'text-gray-400 bg-gray-700 cursor-not-allowed' }} border border-e-0 border-gray-700 rounded-s-lg dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white {{ $prevPage > 0 ? '' : 'dark:cursor-not-allowed' }}">
                                قبلی
                            </a>
                        </li>
                        <li>
                            <span
                                class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-gray-800 border border-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                {{ $pageNumber }}
                            </span>
                        </li>
                        <li>
                            <a href="{{ $nextPage <= $totalPages ? route('bookpage-fv', ['slug' => $book->slug, 'id' => $nextPage]) : '#' }}"
                                class="flex items-center justify-center px-4 h-10 leading-tight {{ $nextPage <= $totalPages ? 'text-gray-500 bg-gray-800 hover:bg-gray-700 hover:text-white' : 'text-gray-400 bg-gray-700 cursor-not-allowed' }} border border-gray-700 rounded-e-lg dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white {{ $nextPage <= $totalPages ? '' : 'dark:cursor-not-allowed' }}">
                                بعدی
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>




    </div>
@endsection

</body>

</html>
