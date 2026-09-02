@extends('auth.layouts.guest')
@section('content')
<h1 class="auth-title">بازیابی رمز عبور</h1><p class="auth-subtitle">ایمیل حسابت را وارد کن تا مراحل بازیابی را ادامه دهی.</p>
<form method="POST" action="{{ route('password.email') }}">@csrf
<div class="field"><label>ایمیل</label><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></div>
<button class="btn-primary" type="submit">ادامه بازیابی</button></form>
<div class="auth-links"><a href="{{ route('login') }}">بازگشت به ورود</a></div>
@endsection