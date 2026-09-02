@extends('admin.layout')
@section('page_title','تحلیل و آمار')
@section('content')
<div class="analytics-hero"><div><h1>تحلیل رفتار و فعالیت</h1><p>این آمار به‌صورت پویا از Eventهای ثبت‌شده توسط برنامه ساخته می‌شود.</p></div><a class="btn btn-secondary" href="{{ route('admin.activities.index') }}">Activity Explorer</a></div>
<div class="analytics-kpis"><div class="metric-card"><span>کاربران فعال امروز</span><strong>{{ $activeUsers }}</strong></div><div class="metric-card"><span>رویدادهای امروز</span><strong>{{ $eventsToday }}</strong></div></div>
@if(!empty($metrics))<section class="card"><div class="card-header"><strong>📈 شاخص‌های سفارشی</strong><span>تعریف‌شده توسط توسعه‌دهنده</span></div><div class="analytics-kpis">@foreach($metrics as $metric)<div class="metric-card"><span>{{ $metric['label'] }}</span><strong>{{ $metric['value'] }}</strong></div>@endforeach</div></section>@endif
<section class="card"><div class="card-header"><strong>🔥 محبوب‌ترین فعالیت‌ها</strong><span>براساس داده واقعی</span></div><div class="top-events">@forelse($topEvents as $event)<div class="event-stat"><div><b>{{ $event->label ?: $event->event }}</b><small>{{ $event->event }}</small></div><strong>{{ $event->total }}</strong></div>@empty<div class="empty-state">هنوز رویدادی برای تحلیل ثبت نشده است.</div>@endforelse</div></section>
<section class="card analytics-help"><strong>توسعه‌پذیر برای هر پروژه</strong><p>ماژول‌های کسب‌وکار در Cyron هاردکد نشده‌اند؛ Eventهای برنامه شما خودکار وارد این Dashboard می‌شوند.</p></section>
@endsection