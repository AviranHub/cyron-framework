@extends('admin.layout')
@section('page_title','پروفایل رفتاری کاربر')
@section('content')
<div class="analytics-hero"><div><h1>{{ $user->name }}</h1><p>{{ $user->email }} · تحلیل رفتار کاربر</p></div><a class="btn btn-secondary" href="{{ route('admin.activities.index') }}">همه فعالیت‌ها</a></div>
<div class="analytics-kpis"><div class="metric-card"><span>کل فعالیت‌ها</span><strong>{{ $totalActivities }}</strong></div><div class="metric-card"><span>روزهای فعال</span><strong>{{ $activeDays }}</strong></div><div class="metric-card"><span>آخرین فعالیت</span><strong>{{ $lastActivity?->occurred_at ?? '-' }}</strong></div></div>
<section class="card"><div class="card-header"><strong>🔥 فعالیت‌های پرتکرار</strong></div>@forelse($topEvents as $event)<div class="event-stat"><div><b>{{ $event->label ?: $event->event }}</b><small>{{ $event->event }}</small></div><strong>{{ $event->total }}</strong></div>@empty<div class="empty-state">فعالیتی ثبت نشده است.</div>@endforelse</section>
<section class="card"><div class="card-header"><strong>🕒 Timeline رفتار</strong></div><div class="timeline">@foreach($activities as $activity)<div class="timeline-item"><time>{{ $activity->occurred_at }}</time><div><b>{{ $activity->label ?: $activity->event ?: $activity->action }}</b><small>{{ $activity->event }}</small></div></div>@endforeach</div></section>
@endsection