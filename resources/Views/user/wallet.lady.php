@extends('user.layouts.app')

@section('content')
<div class="user-dashboard">
    <section class="user-welcome"><div><span class="user-eyebrow">کیف پول</span><h1>موجودی و تراکنش‌های شما</h1><p>موجودی کیف پول برای خریدهای آینده در حساب شما نگهداری می‌شود.</p></div><div class="welcome-mark"><i class="fas fa-wallet"></i></div></section>
    <section class="user-stat-grid"><div class="user-stat"><i class="fas fa-wallet"></i><span>موجودی فعلی</span><strong>{{ number_format((float) ($wallet->balance ?? 0)) }}</strong><small>تومان</small></div><div class="user-stat"><i class="fas fa-receipt"></i><span>تراکنش‌های اخیر</span><strong>{{ count($transactions) }}</strong><small>آخرین ۵۰ تراکنش</small></div></section>
    <section class="user-panel"><div class="user-panel-head"><div><span class="user-eyebrow">افزایش موجودی</span><h2>شارژ کیف پول</h2></div></div><form method="POST" action="{{ route('wallet.recharge') }}" class="profile-form">@csrf<label for="wallet-amount">مبلغ به تومان</label><input id="wallet-amount" name="amount" type="number" min="1000" step="1000" required><button class="user-action" type="submit"><i class="fas fa-credit-card"></i><span><strong>پرداخت و شارژ</strong></span></button></form></section>
    <section class="user-panel"><div class="user-panel-head"><div><span class="user-eyebrow">تاریخچه</span><h2>تراکنش‌ها</h2></div></div>@if(empty($transactions))<p>هنوز تراکنشی برای این حساب ثبت نشده است.</p>@else<div class="profile-fields">@foreach($transactions as $transaction)<div><small>{{ $transaction->created_at }}</small><strong>{{ $transaction->description }}</strong><span>{{ $transaction->type === 'withdraw' ? '-' : '+' }}{{ number_format((float) $transaction->amount) }} تومان</span></div>@endforeach</div>@endif</section>
</div>
@endsection