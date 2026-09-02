@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">ویرایش پروفایل</h1>
    
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif
    
    <div class="bg-white rounded shadow p-6">
        <form method="POST" action="{{ route('user.profile.update') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block mb-1">نام کامل</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border p-2 rounded" required>
                @error('name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block mb-1">ایمیل</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border p-2 rounded" required>
                @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block mb-1">شماره تلفن</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full border p-2 rounded">
                @error('phone') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">ذخیره تغییرات</button>
                <a href="{{ route('user.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">انصراف</a>
            </div>
        </form>
    </div>
</div>
@endsection