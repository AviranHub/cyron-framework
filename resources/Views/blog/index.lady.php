@extends('layouts.master')

@section('content')
<div class="container mx-auto px-4 py-10">
    <header class="mb-8"><span class="text-sm text-green-600">کلبه کتاب / وبلاگ</span><h1 class="text-3xl font-bold mt-2">یادداشت‌های کلبه کتاب</h1><p class="text-gray-500 mt-2">تازه‌ترین مطالب درباره کتاب و مسیر مطالعه.</p></header>
    @if(empty($articles->items()))<p class="text-gray-500">هنوز مقاله‌ای منتشر نشده است.</p>@else<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">@foreach($articles as $article)<article class="bg-white dark:bg-zinc-900 rounded-lg shadow p-5"><span class="text-xs text-gray-500">{{ $article->published_at }}</span><h2 class="text-xl font-bold mt-2"><a href="{{ route('blog.show', ['slug' => $article->slug]) }}">{{ $article->title }}</a></h2><p class="text-gray-600 dark:text-gray-300 mt-3">{{ $article->excerpt }}</p><a class="text-green-600 inline-block mt-4" href="{{ route('blog.show', ['slug' => $article->slug]) }}">ادامه مطلب ←</a></article>@endforeach</div><div class="mt-8">{!! $articles->links() !!}</div>@endif
</div>
@endsection
