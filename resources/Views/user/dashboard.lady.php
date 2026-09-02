@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">داشبورد کاربری</h1>
    
    @if(session()->get('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session()->get('success') }}</div>
    @endif
    
    <div class="bg-white rounded shadow p-6">
        <div class="mb-4">
            <strong>نام:</strong> {{ $user->name }}
        </div>
        <div class="mb-4">
            <strong>ایمیل:</strong> {{ $user->email }}
        </div>
        <div class="mb-4">
            <strong>تلفن:</strong> {{ $user->phone ?? 'ثبت نشده' }}
        </div>
        <div class="mb-4">
            <strong>نقش:</strong> {{ $user->role }}
        </div>
        <div class="mb-4">
            <strong>عضویت از:</strong> {{ $user->created_at }}
        </div>
        
        <div class="mt-6 flex gap-4">
            <a href="{{ route('user.profile.edit') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">ویرایش پروفایل</a>
            <a href="{{ route('user.change-password') }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">تغییر رمز عبور</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">خروج</button>
            </form>
        </div>
    </div>
</div>
@endsection