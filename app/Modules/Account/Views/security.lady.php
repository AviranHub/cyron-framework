@extends('account.layout')
@section('page_title','امنیت حساب')
@section('content')
<div class="analytics-hero"><div><h1>🔐 امنیت حساب</h1><p>رمز عبور، ورود دومرحله‌ای، نشست‌ها و روش‌های بازیابی را مدیریت کنید.</p></div></div>
<div class="security-card-grid">
 <section class="card"><div class="card-body"><h3>🔑 رمز عبور</h3><p>رمز عبور حساب خود را به‌روزرسانی کنید.</p><a class="btn" href="/account/security/password">تغییر رمز</a></div></section>
 <section class="card"><div class="card-body"><h3>🛡️ ورود دومرحله‌ای</h3><p>{{ $two ? 'فعال است' : 'فعال نیست' }}</p><a class="btn" href="/account/security/2fa">مدیریت 2FA</a></div></section>
 <section class="card"><div class="card-body"><h3>📱 نشست‌های فعال</h3><p>دستگاه‌های واردشده به حساب را بررسی کنید.</p><a class="btn" href="/account/security/sessions">مدیریت نشست‌ها</a></div></section>
 <section class="card"><div class="card-body"><h3>🗝️ Recovery Codes</h3><p>برای مواقعی که به روش 2FA دسترسی ندارید.</p><form method="POST" action="{{ route('account.security.recovery') }}"><button class="btn">ایجاد کدهای جدید</button></form></div></section>
</div>
@endsection