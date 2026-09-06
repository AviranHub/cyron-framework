@extends('layouts.master')

@section('keywords')
    کتاب الکترونیکی, کتاب صوتی, رمان فارسی, کتابخوانی اجتماعی, کلبه کتاب
@endsection

@section('description')
    کلبه کتاب؛ جایی برای پیدا کردن کتاب بعدی، دنبال کردن مسیر مطالعه و گفتگو با آدم‌هایی که مثل شما کتاب دوست دارند.
@endsection

@section('content')
<div dir="rtl" class="bg-stone-50 text-slate-800">
    <section class="border-b border-emerald-100 bg-emerald-50" aria-labelledby="home-title">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 sm:px-6 md:py-24 lg:grid-cols-[1.1fr_.9fr] lg:px-8">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-2 text-sm font-bold text-emerald-700 shadow-sm"><i class="fas fa-leaf"></i> کتابخانه‌ای برای مکث و کشف</span>
                <h1 id="home-title" class="mt-5 max-w-3xl text-4xl font-black leading-[1.35] text-emerald-950 sm:text-5xl lg:text-6xl">کتاب بعدی‌ات را <span class="text-cyan-700">با حال خودت</span> پیدا کن.</h1>
                <p class="mt-6 max-w-2xl text-lg leading-9 text-slate-600">از میان رمان‌های محبوب، کتاب‌های تازه و داستان‌هایی که خواننده‌ها پیشنهاد داده‌اند انتخاب کن و مسیر مطالعه‌ات را بساز.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row"><a class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 font-bold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-emerald-700" href="{{ route('books') }}">کشف کتاب‌ها <i class="fas fa-arrow-left"></i></a><a class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-6 font-bold text-cyan-800 transition hover:bg-cyan-100" href="{{ route('register') }}">ساخت کتابخانه من</a></div>
                <div class="mt-8 flex flex-wrap gap-5 text-sm text-slate-500"><span><b class="text-emerald-700">+{{ number_format(count($newestBooks)) }}</b> انتخاب تازه</span><span><b class="text-cyan-700">رایگان</b> برای شروع</span><span><b class="text-amber-700">اجتماعی</b> برای ماندن</span></div>
            </div>
            <div x-data="{ active: 1 }" class="relative flex min-h-[330px] items-center justify-center rounded-[2rem] border border-cyan-100 bg-cyan-50 p-6 shadow-inner sm:min-h-[390px]" aria-label="کتاب‌های محبوب">
                <div class="absolute inset-x-8 bottom-8 h-14 rounded-[50%] bg-cyan-200/70 blur-sm"></div>
                @foreach ($low_books->take(3) as $index => $lowBook)
                    <a href="{{ route('book', ['slug' => $lowBook->slug]) }}" @mouseenter="active = {{ $index }}" class="absolute block overflow-hidden rounded-xl border-4 border-white bg-white shadow-2xl transition duration-500 {{ $index === 0 ? 'h-56 w-40 -translate-x-28 -rotate-6 sm:h-64 sm:w-44 sm:-translate-x-32' : ($index === 1 ? 'z-10 h-64 w-44 -translate-y-5 sm:h-72 sm:w-52' : 'h-56 w-40 translate-x-28 rotate-6 sm:h-64 sm:w-44 sm:translate-x-32') }}" :class="active === {{ $index }} ? 'shadow-emerald-900/20' : ''"><img src="{{ storage_url('/book/' . $lowBook->cover) }}" alt="جلد {{ $lowBook->title }}" loading="{{ $index === 1 ? 'eager' : 'lazy' }}" class="h-full w-full object-cover"></a>
                @endforeach
                <span class="absolute bottom-4 right-5 z-20 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-emerald-700 shadow">این هفته خوانده می‌شود</span>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8" aria-label="شروع کشف کتاب"><div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-amber-100 bg-amber-50 p-5"><div><span class="text-sm font-bold text-amber-700">برای همین لحظه</span><h2 class="mt-1 text-xl font-black text-amber-950">الان دلت چه می‌خواهد؟</h2></div><div class="flex flex-wrap gap-2"><a class="rounded-xl bg-white px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-50" href="{{ route('category.books', ['category' => 'free']) }}"><i class="fas fa-sparkles ml-1"></i> شروع رایگان</a><a class="rounded-xl bg-white px-4 py-3 text-sm font-bold text-cyan-700 shadow-sm transition hover:bg-cyan-100" href="{{ route('category.books', ['category' => 'newest']) }}"><i class="fas fa-wand-magic-sparkles ml-1"></i> تازه‌ها</a><a class="rounded-xl bg-white px-4 py-3 text-sm font-bold text-amber-700 shadow-sm transition hover:bg-amber-100" href="{{ route('forum.index') }}"><i class="fas fa-comments ml-1"></i> گفت‌وگو</a></div></div></section>

    @foreach ([['id' => 'new-books-title', 'kicker' => 'تازه روی قفسه', 'title' => 'تازه‌رسیده‌ها', 'books' => $newestBooks, 'url' => route('category.books', ['category' => 'newest'])], ['id' => 'free-books-title', 'kicker' => 'بدون مانع شروع کن', 'title' => 'خواندن‌های رایگان', 'books' => $freeBooks, 'url' => route('category.books', ['category' => 'free'])]] as $shelf)
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8" aria-labelledby="{{ $shelf['id'] }}"><div class="mb-5 flex items-end justify-between gap-4"><div><span class="text-sm font-bold text-emerald-600">{{ $shelf['kicker'] }}</span><h2 id="{{ $shelf['id'] }}" class="mt-1 text-2xl font-black text-slate-800">{{ $shelf['title'] }}</h2></div><a href="{{ $shelf['url'] }}" class="text-sm font-bold text-emerald-700">مشاهده همه <i class="fas fa-arrow-left mr-1"></i></a></div><div class="flex snap-x gap-4 overflow-x-auto pb-3">@foreach ($shelf['books'] as $book)<a class="group min-w-[155px] snap-start sm:min-w-[180px]" href="{{ route('book', ['slug' => $book->slug]) }}" title="{{ $book->title }}"><div class="overflow-hidden rounded-xl border border-emerald-100 bg-white shadow-sm transition group-hover:-translate-y-1 group-hover:shadow-lg"><div class="relative"><img src="{{ storage_url('/book/' . $book->cover) }}" alt="جلد {{ $book->title }}" loading="lazy" class="aspect-[3/4] w-full object-cover transition duration-500 group-hover:scale-105">@if($book->price == 0)<span class="absolute right-2 top-2 rounded-full bg-emerald-600 px-2 py-1 text-xs font-bold text-white">رایگان</span>@endif</div><div class="p-3"><h3 class="truncate font-bold text-slate-800">{{ $book->title }}</h3><p class="mt-1 truncate text-xs text-slate-500">{{ $book->author_name ?: 'نویسنده مهمان' }}</p></div></div></a>@endforeach</div></section>
    @endforeach

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8" aria-labelledby="community-title"><div class="grid gap-5 md:grid-cols-2"><a href="{{ route('forum.index') }}" class="rounded-2xl border border-cyan-100 bg-cyan-50 p-7 transition hover:-translate-y-1 hover:shadow-lg"><span class="text-3xl">☕</span><h2 id="community-title" class="mt-4 text-2xl font-black text-cyan-950">هر کتاب یک گفت‌وگوست.</h2><p class="mt-2 leading-8 text-cyan-900/70">نظرهایت را ثبت کن، کتاب‌های محبوبت را نگه دار و از خواننده‌های دیگر پیشنهاد بگیر.</p><span class="mt-5 inline-block font-bold text-cyan-800">ورود به انجمن ←</span></a><a href="{{ route('author.books') }}" class="rounded-2xl border border-amber-100 bg-amber-50 p-7 transition hover:-translate-y-1 hover:shadow-lg"><span class="text-3xl">✍️</span><h2 class="mt-4 text-2xl font-black text-amber-950">قصه‌ات را منتشر کن</h2><p class="mt-2 leading-8 text-amber-900/70">اگر نویسنده‌ای، فضای خودت را بساز و کتابت را به خواننده‌ها برسان.</p><span class="mt-5 inline-block font-bold text-amber-800">فضای نویسندگان ←</span></a></div></section>
</div>
@endsection
