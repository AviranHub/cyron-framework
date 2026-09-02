@extends('layouts.master')

{{-- @section('title', 'دسترسی ممنوع - 403') --}}

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <h1 class="text-8xl font-bold text-red-400">403</h1>
    <div class="text-6xl mb-4 mt-4">🚫</div>
    <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-2">دسترسی ممنوع!</h2>
    <p class="text-gray-500 dark:text-gray-400 mb-6">
        شما اجازه دسترسی به این صفحه را ندارید.
    </p>
    <div class="flex gap-4">
        <a href="/" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-home ml-2"></i> صفحه اصلی
        </a>
        @if(Auth::check())
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-sign-out-alt ml-2"></i> خروج
                </button>
            </form>
        @endif
    </div>
</div>
@endsection