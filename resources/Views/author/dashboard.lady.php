@extends('author.layouts.app')
@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-3xl bg-emerald-900 p-6 text-white shadow-xl sm:p-8">
        <div class="max-w-2xl"><span class="text-xs font-extrabold tracking-[.18em] text-amber-200">AUTHOR WORKSPACE</span>
            <h1 class="mt-4 text-3xl font-black leading-tight sm:text-4xl">سلام {{ $user->name ?? 'نویسنده' }}، وقت ساختن است.</h1>
            <p class="mt-3 leading-8 text-emerald-100">آثار خودت را مدیریت کن، صفحات را کامل کن و مسیر انتشار را قدم‌به‌قدم جلو ببر.</p><a href="{{ route('author.books.create') }}" class="mt-6 inline-flex min-h-11 items-center gap-2 rounded-xl bg-amber-300 px-5 font-bold text-emerald-950 transition hover:bg-amber-200"><i class="fas fa-plus"></i> ساخت اثر جدید</a>
        </div>
    </section>
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm"><i class="fas fa-book text-emerald-600"></i>
            <p class="mt-4 text-sm text-slate-500">کل آثار</p><strong class="mt-1 block text-3xl font-black">{{ $stats['books'] }}</strong>
        </div>
        <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-5 shadow-sm"><i class="fas fa-eye text-cyan-700"></i>
            <p class="mt-4 text-sm text-slate-500">بازدید آثار</p><strong class="mt-1 block text-3xl font-black text-cyan-950">{{ number_format($stats['views']) }}</strong>
        </div>
        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-5 shadow-sm"><i class="fas fa-check text-amber-700"></i>
            <p class="mt-4 text-sm text-slate-500">منتشرشده</p><strong class="mt-1 block text-3xl font-black text-amber-950">{{ $stats['published'] }}</strong>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><i class="fas fa-headphones text-slate-500"></i>
            <p class="mt-4 text-sm text-slate-500">نسخه صوتی</p><strong class="mt-1 block text-3xl font-black">{{ $stats['audio'] }}</strong>
        </div>
    </section>
    <section class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
        <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div><span class="text-xs font-bold text-emerald-600">YOUR CATALOG</span>
                    <h2 class="mt-1 text-xl font-black">آخرین آثار</h2>
                </div><a class="text-sm font-bold text-emerald-700" href="{{ route('author.books') }}">مشاهده همه ←</a>
            </div>@if($books->count())<div class="space-y-2">@foreach($books as $book)<a href="{{ route('author.books.edit', ['id' => $book->id]) }}" class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-emerald-50"><span class="grid h-11 w-11 place-items-center rounded-xl bg-emerald-100 text-emerald-700"><i class="fas fa-book"></i></span><span class="min-w-0 flex-1"><strong class="block truncate">{{ $book->title }}</strong><small class="text-slate-500">{{ $book->status ?? 'پیش‌نویس' }} · {{ $book->created_at }}</small></span><i class="fas fa-chevron-left text-slate-400"></i></a>@endforeach</div>@else<div class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-500">هنوز اثری ثبت نکرده‌ای.</div>@endif
        </div>
        <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-5"><span class="text-xs font-bold text-cyan-700">NEXT STEP</span>
            <h2 class="mt-1 text-xl font-black text-cyan-950">مسیر پیشنهادی</h2>
            <div class="mt-5 space-y-3"><a href="{{ route('author.books.create') }}" class="flex items-center gap-3 rounded-xl bg-white p-4 text-cyan-950 shadow-sm"><i class="fas fa-pen-to-square text-cyan-700"></i><span><strong class="block">ساخت پیش‌نویس</strong><small class="text-slate-500">عنوان، جلد و معرفی اثر</small></span></a><a href="{{ route('author.books') }}" class="flex items-center gap-3 rounded-xl bg-white p-4 text-cyan-950 shadow-sm"><i class="fas fa-file-lines text-cyan-700"></i><span><strong class="block">تکمیل صفحات</strong><small class="text-slate-500">محتوای کتاب را اضافه کن</small></span></a></div>
        </div>
    </section>
</div>
@endsection