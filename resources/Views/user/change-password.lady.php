@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">تغییر رمز عبور</h1>
    
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif
    
    <div class="bg-white rounded shadow p-6">
        <form method="POST" action="{{ route('user.change-password') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block mb-1">رمز عبور فعلی</label>
                <input type="password" name="current_password" class="w-full border p-2 rounded" required>
                @error('current_password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block mb-1">رمز عبور جدید</label>
                <input type="password" name="password" class="w-full border p-2 rounded" required>
            </div>
            
            <div class="mb-4">
                <label class="block mb-1">تکرار رمز عبور جدید</label>
                <input type="password" name="password_confirmation" class="w-full border p-2 rounded" required>
                @error('password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">تغییر رمز</button>
                <a href="{{ route('user.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">انصراف</a>
            </div>
        </form>
    </div>
</div>
@endsection