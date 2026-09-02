@extends('auth.layouts.guest')
@section('content')
<h1 class="auth-title">خوش آمدی</h1>
<p class="auth-subtitle">برای ادامه وارد حساب کاربری خود شو.</p>
<form method="POST" action="{{ route('login.submit') }}">
    @csrf
    <div class="field">
        <label>ایمیل، تلفن یا نام کاربری</label>
        <input type="text" name="login" value="{{ old('login') }}" autocomplete="username" required autofocus>
        @error('login') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label>رمز عبور</label>
        <input type="password" name="password" autocomplete="current-password" required>
    </div>
    <button type="submit" class="btn-primary">ورود به حساب</button>
</form>
<nav class="auth-links">
    <a href="{{ route('register') }}">ساخت حساب جدید</a>
    <a href="{{ route('password.request') }}">رمز را فراموش کرده‌ام</a>
</nav>
@endsection