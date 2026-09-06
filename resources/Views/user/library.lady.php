@extends('user.layouts.app')

@section('content')
<div class="user-dashboard">
    @if(session()->get('success'))<div class="user-alert">{{ session()->get('success') }}</div>@endif
    @if(session()->get('error'))<div class="user-alert">{{ session()->get('error') }}</div>@endif
    <section class="user-welcome"><div><span class="user-eyebrow">کتابخانه شخصی</span><h1>کتاب‌هایی که برای خودت نگه داشتی</h1><p>از همین‌جا ادامه بده یا یک کشف تازه به کتابخانه‌ات اضافه کن.</p></div><div class="welcome-mark"><i class="fas fa-books"></i></div></section>
    @if(!empty($shelves))<section class="user-panel"><div class="user-panel-head"><div><span class="user-eyebrow">دسته‌بندی شخصی</span><h2>قفسه‌های من</h2></div></div><div class="profile-fields">@foreach($shelves as $shelf)<a href="{{ route('user.library.shelves.show', ['id' => $shelf->id]) }}"><strong>{{ $shelf->name }}</strong></a>@endforeach</div></section>@endif
    @if(!isset($shelf))<section class="user-panel"><div class="user-panel-head"><div><span class="user-eyebrow">سازمان‌دهی</span><h2>ساخت قفسه</h2></div></div><form method="POST" action="{{ route('user.library.shelves.store') }}" class="profile-form">@csrf<label for="shelf-name">نام قفسه</label><input id="shelf-name" name="name" type="text" required maxlength="191" placeholder="مثلاً کتاب‌های در حال مطالعه"><button class="user-action" type="submit"><i class="fas fa-plus"></i><span><strong>ایجاد قفسه</strong></span></button></form></section>@endif
    @if(empty($books))
        <section class="user-panel empty-library"><i class="fas fa-book-open"></i><h2>کتابخانه‌ات هنوز خالی است</h2><p>یک کتاب رایگان یا یک داستان تازه پیدا کن و به مسیرت اضافه‌اش کن.</p><a class="user-action" href="{{ route('books') }}"><i class="fas fa-compass"></i><span><strong>کشف کتاب‌ها</strong><small>رفتن به کاتالوگ</small></span><i class="fas fa-chevron-left"></i></a></section>
    @else
        <section class="library-grid">
            @foreach($books as $book)
                <a class="library-book" href="{{ route('book', ['slug' => $book->slug]) }}"><img src="{{ storage_url('/book/' . $book->cover) }}" alt="جلد {{ $book->title }}"><div><h2>{{ $book->title }}</h2><p>{{ $book->author_name ?: 'نویسنده مهمان' }}</p><span>ادامه مطالعه <i class="fas fa-arrow-left"></i></span></div></a>
            @endforeach
        </section>
    @endif
</div>
@endsection
