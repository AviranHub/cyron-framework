@extends('layouts.master')

@section('content')
<div class="reading-home">
    <section class="author-hero">
        <div class="author-avatar"><i class="fas fa-feather-pointed"></i></div>
        <div><span class="eyebrow">نویسنده در کلبه کتاب</span><h1>{{ $authorName }}</h1><p>{{ count($books) }} اثر منتشرشده برای خواندن و دنبال‌کردن.</p></div>
        <div class="author-actions">
            @auth
                <form method="POST" action="{{ route('author.follow', ['authorName' => $authorName]) }}">@csrf<button class="button {{ $isFollowing ? 'button--quiet' : 'button--primary' }}" type="submit"><i class="fas fa-{{ $isFollowing ? 'check' : 'plus' }}"></i> {{ $isFollowing ? 'دنبال می‌کنید' : 'دنبال کردن' }}</button></form>
                <a class="button button--quiet" href="#support-author"><i class="fas fa-heart"></i> حمایت از نویسنده</a>
            @else
                <a class="button button--primary" href="{{ route('login') }}">ورود برای دنبال‌کردن <i class="fas fa-arrow-left"></i></a>
            @endauth
        </div>
    </section>

    @if(session('success'))<div class="user-alert">{{ session('success') }}</div>@endif
    @if(session('notice'))<div class="user-alert">{{ session('notice') }}</div>@endif
    @if(session('error'))<div class="user-alert user-alert--error">{{ session('error') }}</div>@endif

    <section class="book-section" aria-labelledby="author-books-title" style="padding-top:2rem"><div class="section-heading"><div><span class="section-kicker">آثار نویسنده</span><h2 id="author-books-title">کتاب‌ها</h2></div></div><div class="book-rail">
        @foreach($books as $book)<a class="book-card" href="{{ route('book', ['slug' => $book->slug]) }}"><div class="book-card__cover"><img src="{{ storage_url('/book/' . $book->cover) }}" alt="جلد {{ $book->title }}" loading="lazy"><span>{{ $book->price == 0 ? 'رایگان' : 'کتاب' }}</span></div><h3>{{ $book->title }}</h3><p>{{ number_format($book->views) }} بازدید</p></a>@endforeach
    </div></section>

    @auth
    <section id="support-author" class="support-panel"><div><span class="section-kicker">حمایت مستقیم</span><h2>از ادامه‌ی نوشتن حمایت کن.</h2><p>مبلغ حمایت بعد از اتصال درگاه پرداخت تکمیل می‌شود و درخواست فعلاً در حساب شما ثبت خواهد شد.</p></div><form method="POST" action="{{ route('author.support', ['authorName' => $authorName]) }}">@csrf<div class="support-amounts"><label><input type="radio" name="amount" value="10000" checked> ۱۰ هزار</label><label><input type="radio" name="amount" value="25000"> ۲۵ هزار</label><label><input type="radio" name="amount" value="50000"> ۵۰ هزار</label></div><button class="button button--primary" type="submit">ثبت حمایت <i class="fas fa-heart"></i></button></form></section>
    @endauth
</div>
@endsection
