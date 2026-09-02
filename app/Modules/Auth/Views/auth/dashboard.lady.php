@extends('auth.layouts.guest')
@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">بازیابی رمز عبور</h2>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label>ایمیل</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border p-2 rounded" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">ارسال لینک بازیابی</button>
    </form>
    <div class="mt-4 text-center"><a href="{{ route('login') }}">بازگشت به ورود</a></div>
</div>
@endsection