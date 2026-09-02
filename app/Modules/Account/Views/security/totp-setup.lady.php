@extends('account.layout')
@section('page_title','تنظیم Authenticator')
@section('content')
<div class="analytics-hero"><div><h1>📱 تنظیم Authenticator</h1><p>این Secret را در برنامه Authenticator خود اضافه کنید و سپس کد تولیدشده را وارد کنید.</p></div></div>
<section class="card"><div class="card-body"><label>Secret</label><code class="totp-secret">{{ $secret }}</code><label>Provisioning URI</label><textarea readonly>{{ $uri }}</textarea><form method="POST" action="/account/security/2fa/totp/confirm"><input name="code" inputmode="numeric" maxlength="6" placeholder="کد ۶ رقمی"><button class="btn">تأیید و فعال‌سازی</button></form></div></section>
@endsection