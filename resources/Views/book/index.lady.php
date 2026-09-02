@extends('layouts.master')

@section('keywords')
@endsection

@section('description')
    {{ $book->title }}
    {{ $book->introduction }}
@endsection

@section('content')
    @php
       // $countComments = count($comments);
    @endphp
    <div class="container mt-6">
        <div class="container mt-6 py-6">
            <div class="flex items-center bg-white dark:bg-zinc-900 p-4 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div
                        class="flex items-center justify-center w-10 h-10 ml-2 bg-green-100 dark:bg-green-900 rounded-full mr-2">
                        <i class="fa fa-home text-green-600 dark:text-green-300"></i>
                    </div>
                    <a href="{{ route('home') }}" class="text-green-600 hover:underline">کلبه کتاب</a>
                    <i class="fa fa-chevron-left mx-2 text-gray-400"></i>
                </div>
                <a href="{{ route('books') }}" class="text-green-600 hover:underline">کتاب ها</a>
                <i class="fa fa-chevron-left mx-2 text-gray-400"></i>
                <span class="text-gray-600">{{ $book->title }}</span>
            </div>
        </div>
    </div>
    <div class="container mx-auto mx-4">
        <div class="flex flex-col p-2 bg-white dark:bg-zinc-900 shadow-lime justify-between gap-10 rounded-lg">
            <div class="flex lg:flex-row flex-col">
                <div class="flex p-2 lg:flex-row flex-col text-gray-800 dark:text-gray-300 gap-6">
                    <div class="book-cover-box relative">
                        <div class="absolute top-3 right-3 flex items-center">
                            <div
                                class="w-3 h-3 rounded-full 
                                    @if ($book->status == 'published') bg-green-500
                                    @elseif($book->status == 'draft') bg-yellow-500
                                    @else bg-red-500 @endif
                                    shadow-md">
                            </div>
                            <span class="mr-2 text-xs bg-black bg-opacity-70 text-white px-2 py-1 rounded-md">
                                @if ($book->status == 'published')
                                    منتشر شده
                                @elseif($book->status == 'draft')
                                    پیش‌نویس
                                @else
                                    غیرفعال
                                @endif
                            </span>
                        </div>
                        {{-- <img src="{{ asset('storage/book/' . $book->cover) }}" alt="جلد کتاب {{ $book->title }}"
                                class="lg:w-[300px] w-full h-[500px] lg:h-[400px] rounded-lg"> --}}

                        <img src="{{ storage_url('/book/' . $book->cover) }}" alt="جلد کتاب {{ $book->title }}"
                            class="lg:w-[300px] w-full h-[500px] lg:h-[400px] object-cover rounded-lg shadow-lg transition-transform duration-300 hover:scale-105">
                        @if ($book->is_audio)
                            <div
                                class="absolute bottom-3 right-3 text-rose-400 bg-black bg-opacity-70 py-1 px-2 rounded-lg text-xl">
                                <i class="fa fa-headphones"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col p-2 font-vazir text-xl gap-2">
                        <h1 class="font-yekan text-4xl my-2 text-gray-900 dark:text-white">{{ $book->title }}</h1>


                        <div class="flex items-center gap-2">
                            <i class="fa fa-user text-blue-500"></i>
                            <p class="m-0">نویسنده :
                                @if ($book->author_id == 'author')
                                    <a class="text-blue-600 hover:underline"
                                        href="{{ route('author', ['id' => $book->publisher_id]) }}">{{ $book->author }}</a>
                                @else
                                    <span class="text-green-500 font-medium">{{ $book->author_name }}</span>
                                @endif
                            </p>
                        </div>


                        @if ($genre)
                            <div class="flex items-center gap-2">
                                <i class="fa fa-tags text-purple-500"></i>
                                <p class="m-0">ژانر : <span
                                        class="text-green-500 font-medium">{{ $genre->name }}</span></p>
                            </div>
                        @endif


                        <!-- تعداد صفحات -->
                        <div class="flex items-center gap-2">
                            <i class="fa fa-file-text text-orange-500"></i>
                            <p class="m-0">تعداد صفحات : <span
                                    class="text-gray-700 dark:text-gray-300 font-medium">{{ $book->pages }} صفحه</span>
                            </p>
                        </div>


                        <!-- نمایش قیمت با تخفیف -->
                        <div class="flex items-center gap-2">
                            <i class="fa fa-tag text-red-500"></i>
                            <p class="m-0">قیمت :
                                @if ($book->copen == 100 or $book->price == 0)
                                    <span class="text-green-500 font-bold text-xl">رایگان</span>
                                @else
                                    @if ($book->copen > 0)
                                        <span
                                            class="text-gray-500 line-through ml-2">{{ number_format($book->price) }}</span>
                                        @php
                                            $discountedPrice = $book->price - ($book->price * $book->copen) / 100;
                                        @endphp
                                        <span
                                            class="text-green-500 font-bold text-xl">{{ number_format($discountedPrice) }}</span>
                                        <span
                                            class="bg-red-500 text-white text-sm px-2 py-1 rounded-md mr-2">{{ $book->copen }}%</span>
                                    @else
                                        <span
                                            class="text-green-500 font-bold text-xl">{{ number_format($book->price) }}</span>
                                    @endif
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">تومان</span>
                                @endif
                            </p>
                        </div>

                        @if ($book->description)
                            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg max-w-[500px]">
                                <p class="text-gray-700 dark:text-gray-300 text-sm text-wrap">
                                    {{ $book->description }}
                                </p>
                            </div>
                        @endif

                    </div>
                </div>




                <div class="inline-flex flex-col items-center justify-center p-1 font-vazir text-lg gap-4 w-full lg:w-1/3">
                    @isset ($hasBook)
                        @empty($book->pdf)
                            <a href="{{ route('online-ready', ['slug' => $book->slug, 'id' => 1]) }}"
                                class="w-48 text-green-500 rounded-lg border-2 border-green-500 text-decoration-none text-center py-2 px-6">مطالعه
                                آنلاین</a>
                        @else
                            <a href="{{ $book->pdf }}" download
                                class="w-48 transition-all duration-5000 text-rose-500 rounded-lg border-2 border-rose-500 bg-transparent hover:bg-rose-500 hover:text-gray-950 text-decoration-none text-center py-2 px-6">
                                دانلود فایل pdf
                            </a>
                        @endempty
                    @else
                        @if ($book->copen == 100 or $book->price == 0)
                            <a href="{{ route('add-to-library', ['slug' => $book->slug]) }}"
                                class="w-48 h-12 inline-flex items-center justify-center text-green-500 dark:text-white dark:bg-green-500 rounded-lg border-2 border-green-500 text-decoration-none text-center">
                                <i class="fa fa-plus ml-2"></i>
                                افزودن به کتابخانه
                            </a>
                        @else
                            <a href="{{ route('book.buy', ['slug' => $book->slug]) }}"
                                class="w-48 text-green-500 rounded-lg border-2 border-green-500 text-decoration-none text-center py-2 px-6">خرید
                                کتاب</a>
                        @endif
                        <a href="{{ route('bookpage-fv', ['slug' => $book->slug, 'id' => 1]) }}"
                            class="w-48 text-black dark:text-white rounded-lg border-2 border-black dark:border-white text-decoration-none text-center py-2 px-6">نسخه
                            نمونه</a>
                    @endisset
                    <div class="flex flex-col mt-3">
                        <p class="text-lg p-2 text-zinc-800 dark:text-zinc-100">آیا این کتاب مورد پسند شما بود؟</p>
                        <div class="flex gap-3 justify-center text-xl">
                            <div
                                class="inline-flex justify-center items-center bg-zinc-800 text-zinc-100 cursor-pointer p-2 rounded-full hover:bg-green-600 hover:text-white">
                                <i class="far fa-thumbs-up"></i></div>
                            <div
                                class="inline-flex justify-center items-center bg-zinc-800 text-zinc-100 cursor-pointer p-2 rounded-full hover:bg-red-600 hover:text-white">
                                <i class="far fa-thumbs-down"></i></div>
                            {{-- <div class="inline-flex justify-center items-center bg-zinc-700 p-2"><i class="far fa-bookmark"></i></div> --}}
                        </div>
                    </div>
                </div>
            </div>

            <!-- بخش آمار کتاب -->
            <div
                class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-zinc-950 dark:to-zinc-900 rounded-xl shadow-inner">
                <h3 class="font-yekan text-2xl mb-4 text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fa fa-chart-bar text-blue-500"></i>
                    آمار کتاب
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div
                        class="flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-800 rounded-lg shadow-sm transition-transform duration-300 hover:scale-105">
                        <i class="fa fa-eye text-blue-500 text-2xl mb-2"></i>
                        <span class="text-gray-600 dark:text-gray-400 text-sm">بازدید</span>
                        <span class="text-gray-800 dark:text-white font-bold text-xl">{{ $book->views }}</span>
                    </div>

                    <div
                        class="flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-800 rounded-lg shadow-sm transition-transform duration-300 hover:scale-105">
                        <i class="fa fa-heart text-red-500 text-2xl mb-2"></i>
                        <span class="text-gray-600 dark:text-gray-400 text-sm">لایک</span>
                        <span class="text-gray-800 dark:text-white font-bold text-xl">{{ $book->likes }}</span>
                    </div>

                    <div
                        class="flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-800 rounded-lg shadow-sm transition-transform duration-300 hover:scale-105">
                        <i class="fa fa-calendar text-green-500 text-2xl mb-2"></i>
                        <span class="text-gray-600 dark:text-gray-400 text-sm">تاریخ انتشار</span>
                        <?php error_log(" log : ".$book->created_at); ?>
                        <span
                            class="text-gray-800 dark:text-white font-bold text-sm">{{ jdate('Y/m/d',strtotime($book->created_at)) }}</span>
                    </div>

                    <div
                        class="flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-800 rounded-lg shadow-sm transition-transform duration-300 hover:scale-105">
                        <i class="fa fa-comments text-purple-500 text-2xl mb-2"></i>
                        <span class="text-gray-600 dark:text-gray-400 text-sm">نظرات</span>
                        <span class="text-gray-800 dark:text-white font-bold text-xl">{{ $countComments ?? 0 }}</span>
                    </div>

                    <div
                        class="flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-800 rounded-lg shadow-sm transition-transform duration-300 hover:scale-105">
                        <i class="fa fa-file-text text-orange-500 text-2xl mb-2"></i>
                        <span class="text-gray-600 dark:text-gray-400 text-sm">صفحات</span>
                        <span class="text-gray-800 dark:text-white font-bold text-xl">{{ $book->pages }}</span>
                    </div>

                    <div
                        class="flex flex-col items-center justify-center p-4 bg-white dark:bg-zinc-800 rounded-lg shadow-sm transition-transform duration-300 hover:scale-105">
                        <i class="fa fa-percentage text-red-500 text-2xl mb-2"></i>
                        <span class="text-gray-600 dark:text-gray-400 text-sm">تخفیف</span>
                        <span class="text-gray-800 dark:text-white font-bold text-xl">{{ $book->copen }}%</span>
                    </div>
                </div>
            </div>


        </div>
    </div>
    </div>

    <div class="">
        @isset($similars)
            <div class="container mx-auto lg:px-4 py-10">
                <div class="bookbox mb-8">
                    <div class="grid grid-cols-2 gap-4 items-center mb-4">
                        <div class="text-right">
                            <div class="text-xl font-bold dark:text-gray-300">
                                کتاب های مشابه
                            </div>
                        </div>
                        <div class="text-left"></div>
                    </div>
                    <div class="overflow-x-auto overflow-y-hidden">
                        <div class="flex space-x-4">
                            <div class="flex gap-2 flex-row">
                                @foreach ($similars as $similarBook)
                                    <a class="block w-36" href="{{ route('book', ['slug' => $similarBook->slug]) }}"
                                        title="مشاهده جزئیات کتاب {{ $similarBook->title }}"
                                        aria-label="مشاهده جزئیات کتاب {{ $similarBook->title }}">
                                        <img alt="جلد کتاب {{ $similarBook->title }}"
                                            class="book-cover mb-2 w-36 h-52 object-cover"
                                            src="{{ storage_url('/book/' . $similarBook->cover) }}" />
                                        <div class="text-center">
                                            <p class="text-lg font-bold m-0 dark:text-gray-400">
                                                {{ $similarBook->title }}
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-500 m-0">
                                                نویسنده: {{ $similarBook->author_name }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endisset

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 mx-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-2"></div>

        <div class="container my-4 px-8">
            <div id="commentform"></div>

            @auth
                <form class="flex flex-col gap-4" method="POST"
                    action="{{ route('save-comments', ['slug' => $book->slug]) }}">
                    @csrf
                    <input type="hidden" name="commentable_type" value="App\Models\Book">
                    <input type="hidden" name="commentable_id" value="{{ $book->id }}">
                    <input type="hidden" name="reply_id" id="replayed">

                    <label class="field-label text-xl dark:text-gray-200" for="comment">نظر شما</label>
                    <textarea class="text-gray-900 dark:text-gray-200 rounded-lg bg-white dark:bg-zinc-700 h-48" type="text"
                        name="text" id="comment" placeholder="نظر شما ..." required></textarea>

                    <div class="container py-2">
                        <input class="btn-submit bg-blue-500 text-white py-2 px-4 rounded-lg" type="submit" value="ارسال">
                    </div>
                </form>
            @else
                <div class="flex flex-col justify-center gap-1 p-1 text-center">
                    <p class="text-xl alert-danger text-red-500">برای ثبت دیدگاه خود باید وارد شوید</p>
                    <a class="shadow-lime text-lg text-blue-500 dark:text-blue-400 rounded-lg text-decoration-none font-vazir"
                        href="{{ route('login') }}" role="button">
                        <i class="fa fa-hand-point-left text-orange-300 dark:text-orange-200" aria-hidden="true"></i>
                        عضویت - ورود
                    </a>
                    <div class="space-1"></div>
                </div>
            @endauth
        </div>

        <h2 class="font-vazir text-center text-gray-900 dark:text-gray-100 text-2xl my-6">نظرات کاربران</h2>

        <p class="font-vazir text-align-right p-1 mx-4 r-10d position-relative text-gray-900 dark:text-gray-100 text-lg">
            {{ $countComments }} دیدگاه
        </p>
{{--
        <div class="container">
            <div class="flex flex-col w-full gap-6">
                @foreach ($comments as $comment)
                    <div class="flex flex-col bg-white dark:bg-zinc-900 rounded-lg shadow-lg p-4"
                        id="comment-{{ $comment->id }}">
                        <!-- هدر کامنت -->
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                @if ($comment->author->photo ?? false)
                                    <img src="{{ asset('storage/uploads/' . $comment->author->photo) }}"
                                        class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <x-user-avatar class="w-12 h-12 rounded-full" />
                                @endif
                                <div>
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ $comment->author_name }}
                                        </span>

                                        <!-- نمایش نقش کاربر -->
                                        @if ($comment->author->role ?? false)
                                            <span
                                                class="text-xs px-2 py-1 rounded-full 
                        @switch($comment->author->role)
                            @case('admin') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 @break
                            @case('author') bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 @break
                            @case('ceo') bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100 @break
                            @case('publisher') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 @break
                            @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                        @endswitch">
                                                @switch($comment->author->role)
                                                    @case('admin')
                                                        👑 مدیر
                                                    @break

                                                    @case('author')
                                                        ✍️ نویسنده
                                                    @break

                                                    @case('ceo')
                                                        💼 مدیرعامل
                                                    @break

                                                    @case('publisher')
                                                        📚 ناشر
                                                    @break
                                                @endswitch
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            @auth
                                <div class="flex p-2 font-vazir cursor-pointer items-center text-blue-500 hover:text-blue-700"
                                    onclick="commentReply('{{ $comment->author_name }}','{{ $comment->text }}','{{ $comment->id }}')">
                                    <i class="fa fa-reply ml-1"></i>
                                    <span class="text-sm">پاسخ</span>
                                </div>
                            @endauth
                        </div>

                        <!-- کامنت والد (اگر پاسخ هست) -->
                        @if ($comment->parent)
                            <div class="mb-3 p-3 bg-gray-50 dark:bg-zinc-800 rounded-lg border-r-4 border-green-500  cursor-pointer"
                                onclick="gotoComment('{{ $comment->reply_id }}')">
                                <div class="flex items-center space-x-2 space-x-reverse mb-2">
                                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                        {{ $comment->parent->author_name }}
                                    </span>
                                    <span class="text-xs text-gray-500">نوشته:</span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                                    {{ $comment->parent->text }}
                                </p>
                            </div>
                        @endif

                        <!-- متن کامنت اصلی -->
                        <div class="text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">
                            {{ $comment->text }}
                        </div>


                        <!-- آمار کامنت با انیمیشن -->
                        <div
                            class="flex items-center space-x-6 space-x-reverse text-sm border-t border-gray-200 dark:border-gray-600 pt-3">
                            <!-- دکمه لایک -->
                            <button onclick="toggleLike('App\\Models\\Comment', {{ $comment->id }}, 'like')"
                                class="like-btn group flex items-center space-x-2 space-x-reverse transition-all duration-300 
                   {{ $comment->userLike && $comment->userLike->is_like ? 'text-green-500 scale-110' : 'text-gray-500 dark:text-gray-400 hover:text-green-500' }}">
                                <i class="fa-regular fa-thumbs-up text-lg group-hover:scale-110 transition-transform"></i>
                                <span id="comment-likes-{{ $comment->id }}"
                                    class="text-sm font-medium transition-all duration-300">
                                    {{ $comment->likes_count }}
                                </span>
                            </button>

                            <!-- دکمه دیسلایک -->
                            <button onclick="toggleLike('App\\Models\\Comment', {{ $comment->id }}, 'dislike')"
                                class="dislike-btn group flex items-center space-x-2 space-x-reverse transition-all duration-300 
                   {{ $comment->userLike && !$comment->userLike->is_like ? 'text-red-500 scale-110' : 'text-gray-500 dark:text-gray-400 hover:text-red-500' }}">
                                <i
                                    class="fa-regular fa-thumbs-down text-lg group-hover:scale-110 transition-transform"></i>
                                <span id="comment-dislikes-{{ $comment->id }}"
                                    class="text-sm font-medium transition-all duration-300">
                                    {{ $comment->dislikes_count }}
                                </span>
                            </button>

                            <!-- تعداد پاسخ‌ها -->
                            <div
                                class="flex items-center space-x-2 space-x-reverse text-blue-500 group cursor-pointer hover:text-blue-600 transition-colors">
                                <i class="fa-regular fa-comment text-lg group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm font-medium">{{ $comment->replies_count }} پاسخ</span>
                            </div>
                        </div>

                    </div>
                @endforeach

                @if ($comments->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400 text-lg">هنوز نظری ثبت نشده است.</p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">اولین نفری باشید که نظر می‌دهد!</p>
                    </div>
                @endif
            </div>
        </div>
         --}}
    </div>
@endsection

@section('script')
    <script>
        let isReplyComment = false;

        function commentReply(replyedName, replyedMsg, replyedId) {
            if (isReplyComment) {
                document.getElementById(`repbox`).remove();
                isReplyComment = false;
            }

            let replyBox = document.createElement('div');
            replyBox.id = "repbox";
            replyBox.className =
                'fixed w-full bg-white dark:bg-gray-900 bottom-0 z-20 border-t border-gray-300 dark:border-gray-600 shadow-lg';

            let gridDiv = document.createElement('div');
            gridDiv.className = 'flex flex-row-reverse justify-between items-center pr-2 font-irsans';

            let closeButton = document.createElement('button');
            closeButton.className = 'px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-red-500 transition-colors';
            closeButton.onclick = closeCommentReply;
            closeButton.innerHTML = '<i class="fa fa-times text-lg"></i>';

            let replySection = document.createElement('div');
            replySection.className = 'flex items-center space-x-3 space-x-reverse flex-1';

            let replyIcon = document.createElement('div');
            replyIcon.className = 'text-blue-500';
            replyIcon.innerHTML = '<i class="fa fa-reply text-xl"></i>';

            let replyTextContainer = document.createElement('div');
            replyTextContainer.className = 'flex flex-col';

            let replyToText = document.createElement('div');
            replyToText.className = 'text-sm font-semibold dark:text-gray-200';
            replyToText.innerHTML = 'پاسخ به <span class="text-blue-500">' + replyedName + '</span>';

            let replyMsgText = document.createElement('div');
            replyMsgText.className = 'text-xs text-gray-600 dark:text-gray-400 truncate max-w-xs';
            replyMsgText.innerHTML = replyedMsg;

            replyTextContainer.appendChild(replyToText);
            replyTextContainer.appendChild(replyMsgText);

            replySection.appendChild(replyIcon);
            replySection.appendChild(replyTextContainer);

            gridDiv.appendChild(closeButton);
            gridDiv.appendChild(replySection);

            replyBox.appendChild(gridDiv);
            document.body.appendChild(replyBox);

            document.getElementById('replayed').value = replyedId;
            document.getElementById('comment').placeholder = 'در پاسخ به ' + replyedName + '...';

            document.getElementById('commentform').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            isReplyComment = true;
        }

        function closeCommentReply() {
            const replyBox = document.getElementById('repbox');
            if (replyBox) {
                replyBox.remove();
            }
            isReplyComment = false;
            document.getElementById('replayed').value = '';
            document.getElementById('comment').placeholder = 'نظر شما ...';
        }

        function gotoComment(commentId) {
            const targetComment = document.getElementById('comment-' + commentId);
            if (targetComment) {
                targetComment.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                targetComment.style.transition = 'all 0.5s ease';
                targetComment.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';

                setTimeout(() => {
                    targetComment.style.backgroundColor = '';
                }, 3000);
            }
        }

        function toggleLike(likeableType, likeableId, action) {
            if (!{{ auth()->check() ? 'true' : 'false' }}) {
                window.location.href = '{{ route('login') }}';
                return;
            }

            fetch('{{ route('toggle.like') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        likeable_type: likeableType,
                        likeable_id: likeableId,
                        action: action
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // آپدیت اعداد
                    document.getElementById(`comment-likes-${likeableId}`).textContent = data.likes_count;
                    document.getElementById(`comment-dislikes-${likeableId}`).textContent = data.dislikes_count;

                    // آپدیت استایل دکمه‌ها
                    updateCommentButtonStyles(likeableId, data.user_action);
                })
                .catch(error => console.error('Error:', error));
        }

        function updateCommentButtonStyles(commentId, userAction) {
            const likeBtn = document.querySelector(
                `[onclick="toggleLike('App\\\\\\\\Models\\\\\\\\Comment', ${commentId}, 'like')"]`);
            const dislikeBtn = document.querySelector(
                `[onclick="toggleLike('App\\\\\\\\Models\\\\\\\\Comment', ${commentId}, 'dislike')"]`);

            // ریست استایل‌ها
            likeBtn.classList.remove('text-green-500', 'text-gray-500', 'dark:text-gray-400');
            dislikeBtn.classList.remove('text-red-500', 'text-gray-500', 'dark:text-gray-400');

            // ست کردن استایل جدید
            if (userAction === 'like') {
                likeBtn.classList.add('text-green-500');
                dislikeBtn.classList.add('text-gray-500', 'dark:text-gray-400');
            } else if (userAction === 'dislike') {
                dislikeBtn.classList.add('text-red-500');
                likeBtn.classList.add('text-gray-500', 'dark:text-gray-400');
            } else {
                likeBtn.classList.add('text-gray-500', 'dark:text-gray-400');
                dislikeBtn.classList.add('text-gray-500', 'dark:text-gray-400');
            }
        }
    </script>
@endsection
