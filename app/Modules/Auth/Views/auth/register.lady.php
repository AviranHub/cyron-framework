@extends('auth.layouts.guest')
@section('content')
<h1 class="auth-title">ساخت حساب</h1><p class="auth-subtitle">اطلاعاتت را وارد کن تا حسابت ساخته شود.</p>
<form method="POST" action="{{ route('register.submit') }}">@csrf
<div class="field"><label>نام کامل</label><input name="name" value="{{ old('name') }}" autocomplete="name" required>@error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror</div>
<div class="field"><label>ایمیل</label><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></div>
<div class="field"><label>شماره تلفن <small>(اختیاری)</small></label><input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"></div>
<div class="field"><label>رمز عبور</label><input type="password" name="password" autocomplete="new-password" required></div>
<div class="field"><label>تکرار رمز عبور</label><input type="password" name="password_confirmation" autocomplete="new-password" required></div>
<button class="btn-primary" type="submit">ساخت حساب</button></form>
<div class="auth-links"><a href="{{ route('login') }}">قبلاً حساب ساخته‌ام</a></div>
@endsection