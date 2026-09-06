@extends('layouts.master')

@section('content')
<article class="container mx-auto max-w-3xl px-4 py-10"><a class="text-green-600" href="{{ route('blog.index') }}">← بازگشت به وبلاگ</a><header class="mt-6"><span class="text-sm text-gray-500">{{ $article->published_at }}</span><h1 class="text-4xl font-bold mt-2">{{ $article->title }}</h1><p class="text-gray-500 mt-4">{{ $article->excerpt }}</p></header><div class="mt-8 prose max-w-none dark:prose-invert">{!! $article->content !!}</div></article>
@endsection
