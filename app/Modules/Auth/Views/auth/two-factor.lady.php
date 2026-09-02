@extends('auth.layouts.guest')
@section('content')
<h1 class="auth-title">تأیید ورود</h1>
<p class="auth-subtitle">کد ورود دومرحله‌ای را وارد کن تا ورود کامل شود.</p>
<form method="POST" action="{{ route('login.two-factor.verify') }}">
    @csrf
    <div class="field">
        <label>کد تأیید</label>
        <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="8" required autofocus>
    </div>
    <button type="submit" class="btn-primary">تأیید و ورود</button>
</form>
<nav class="auth-links">
    <a href="{{ route('login') }}">بازگشت به ورود</a>
</nav>
@endsection