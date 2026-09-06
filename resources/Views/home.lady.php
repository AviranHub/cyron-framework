@extends('layouts.master')

@section('content')
<div dir="rtl" class="bg-stone-50 text-slate-800">
    <section class="relative overflow-hidden border-b border-emerald-100 bg-emerald-50">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 sm:px-6 md:py-24 lg:grid-cols-[1.05fr_.95fr] lg:px-8">
            <div class="relative z-10">
                <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white/80 px-4 py-2 text-sm font-bold text-emerald-700 shadow-sm"><i class="fa fa-leaf"></i> خواندن، آرام‌تر از همیشه</p>
                <h1 class="max-w-3xl text-4xl font-black leading-[1.35] text-emerald-950 sm:text-5xl lg:text-6xl">جایی برای پیدا کردن کتاب بعدی‌ات</h1>
                <p class="mt-6 max-w-2xl text-lg leading-9 text-slate-600">در کلبه کتاب، میان داستان‌ها قدم بزن، نویسنده‌های تازه را کشف کن و با آدم‌هایی که کتاب دوست دارند گفت‌وگو کن.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('books') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 font-bold text-white shadow-lg shadow-emerald-900/10 transition hover:-translate-y-0.5 hover:bg-emerald-700"><i class="fa fa-book-open"></i> دیدن کتاب‌ها</a>
                    <a href="{{ route('forum.index') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-6 font-bold text-cyan-800 transition hover:bg-cyan-100"><i class="fa fa-comments"></i> رفتن به تالار گفتگو</a>
                </div>
                <div class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-500"><span><i class="fa fa-check ml-1 text-emerald-600"></i> کشف کتاب‌های تازه</span><span><i class="fa fa-check ml-1 text-emerald-600"></i> فضای گفت‌وگوی آرام</span></div>
            </div>
            <div x-data="{ active: 1 }" class="relative mx-auto flex min-h-[330px] w-full max-w-lg items-center justify-center rounded-[2rem] border border-cyan-100 bg-cyan-50 p-6 shadow-inner sm:min-h-[390px]">
                <div class="absolute inset-x-8 bottom-8 h-16 rounded-[50%] bg-cyan-200/60 blur-sm"></div>
                @foreach ($low_books->take(3) as $index => $low_book)
                    <a href="{{ route('book', ['slug' => $low_book->slug]) }}" @mouseenter="active = {{ $index }}" class="absolute block overflow-hidden rounded-xl border-4 border-white bg-white shadow-2xl transition duration-500 {{ $index === 0 ? 'h-56 w-40 -translate-x-28 -rotate-6 sm:h-64 sm:w-44 sm:-translate-x-32' : ($index === 1 ? 'z-10 h-64 w-44 -translate-y-5 sm:h-72 sm:w-52' : 'h-56 w-40 translate-x-28 rotate-6 sm:h-64 sm:w-44 sm:translate-x-32') }}" :class="active === {{ $index }} ? 'shadow-emerald-900/20' : ''">
                        <img src="{{ asset('/storage/book/' . $low_book->cover) }}" alt="{{ $low_book->title }}" loading="lazy" class="h-full w-full object-cover">
                    </a>
                @endforeach
                <span class="absolute bottom-4 right-5 z-20 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-emerald-700 shadow">یک فصل دیگر؟</span>
            </div>
        </div>
    </section>

    @foreach ([['title' => 'جدیدترین‌ها', 'books' => $newestBooks, 'url' => route('category.books', ['category' => 'newest']), 'tone' => 'emerald'], ['title' => 'کتاب‌های رایگان', 'books' => $freeBooks, 'url' => route('category.books', ['category' => 'free']), 'tone' => 'cyan']] as $shelf)
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-5 flex items-end justify-between gap-4"><div><p class="text-sm font-bold text-{{ $shelf['tone'] }}-600">برای مکث‌های کوتاه</p><h2 class="mt-1 text-2xl font-black text-slate-800">{{ $shelf['title'] }}</h2></div><a href="{{ $shelf['url'] }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-900">مشاهده همه <i class="fa fa-arrow-left mr-1"></i></a></div>
        <div class="flex snap-x gap-4 overflow-x-auto pb-3">
            @foreach (array_slice(is_object($shelf['books']) ? $shelf['books']->toArray() : $shelf['books'], 0, 8) as $book)
            <a href="{{ route('book', ['slug' => $book->slug]) }}" class="group min-w-[155px] snap-start sm:min-w-[180px]" title="{{ $book->title }}">
                <div class="overflow-hidden rounded-xl border border-emerald-100 bg-white shadow-sm transition group-hover:-translate-y-1 group-hover:shadow-lg"><img src="{{ asset('/storage/book/' . $book->cover) }}" alt="{{ $book->title }}" loading="lazy" class="aspect-[3/4] w-full object-cover transition duration-500 group-hover:scale-105"><div class="p-3"><h3 class="truncate font-bold text-slate-800">{{ $book->title }}</h3><p class="mt-1 truncate text-xs text-slate-500">{{ $book->author_name }}</p><p class="mt-2 text-sm font-bold text-emerald-700">{{ ($book->copen == 100 or $book->price == 0) ? 'رایگان' : number_format($book->price) . ' تومان' }}</p></div></div>
            </a>
            @endforeach
        </div>
    </section>
    @endforeach

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="grid gap-5 md:grid-cols-2"><a href="{{ route('forum.index') }}" class="group rounded-2xl border border-cyan-100 bg-cyan-50 p-7 transition hover:-translate-y-1 hover:shadow-lg"><span class="text-3xl">☕</span><h2 class="mt-4 text-2xl font-black text-cyan-950">گفت‌وگوهای آرام درباره کتاب</h2><p class="mt-2 leading-8 text-cyan-900/70">موضوعی بساز، تجربه‌ات را بنویس و از خواننده‌های دیگر پیشنهاد بگیر.</p><span class="mt-5 inline-block font-bold text-cyan-800">ورود به انجمن ←</span></a><a href="{{ route('author.books') }}" class="group rounded-2xl border border-amber-100 bg-amber-50 p-7 transition hover:-translate-y-1 hover:shadow-lg"><span class="text-3xl">✍️</span><h2 class="mt-4 text-2xl font-black text-amber-950">قصه‌ات را منتشر کن</h2><p class="mt-2 leading-8 text-amber-900/70">اگر نویسنده‌ای، فضای خودت را بساز و کتابت را به خواننده‌ها برسان.</p><span class="mt-5 inline-block font-bold text-amber-800">فضای نویسندگان ←</span></a></div>
    </section>
</div>
@endsection
