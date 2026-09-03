@extends('admin.layout')

@section('content')
<div class="admin-dashboard">
    <section class="dashboard-hero">
        <div><span class="eyebrow">CYRON / CONTROL CENTER</span><h1>سلام، مدیر سیستم</h1><p>نمایی زنده از وضعیت محتوا، کاربران و فعالیت‌های امروز شما.</p></div>
        <div class="system-status"><span></span> سیستم فعال <small>{{ date('Y/m/d') }}</small></div>
    </section>

    <section class="kpi-grid" aria-label="آمار اصلی">
        @foreach($stats as $key => $stat)
            @if(!is_array($stat) || !isset($stat['label'], $stat['count'])) @continue @endif
            <a class="kpi-card kpi-{{ $stat['color'] ?? 'blue' }}" href="{{ $stat['route'] ?? route('admin.dashboard') }}"><div class="kpi-top"><span>{{ $stat['label'] }}</span><i class="fas fa-{{ $stat['icon'] ?? 'chart-line' }}"></i></div><strong>{{ number_format($stat['count']) }}</strong><div class="kpi-foot"><span>مشاهده بخش</span><b>←</b></div></a>
        @endforeach
        <div class="kpi-card kpi-cyan"><div class="kpi-top"><span>فعالیت امروز</span><i class="fas fa-bolt"></i></div><strong>{{ number_format($todayActivities ?? 0) }}</strong><div class="kpi-foot"><span>رویداد ثبت‌شده</span><b>●</b></div></div>
    </section>

    <section class="dashboard-grid">
        <div class="dashboard-panel activity-panel"><div class="panel-heading"><div><span class="eyebrow">LIVE FEED</span><h2>آخرین فعالیت‌ها</h2></div><a href="{{ route('admin.dashboard') }}">همه فعالیت‌ها</a></div>
            @if(isset($recentActivities) && $recentActivities->count())<div class="activity-list">@foreach($recentActivities as $activity)<div class="activity-row"><span class="activity-dot"></span><div><strong>{{ $activity->action }}</strong><small>{{ $activity->user->name ?? 'کاربر ناشناس' }}</small></div><time>{{ $activity->created_at }}</time></div>@endforeach</div>@else<div class="empty-state"><i class="fas fa-wave-square"></i><p>هنوز فعالیتی ثبت نشده است.</p></div>@endif
        </div>
        <div class="dashboard-panel users-panel"><div class="panel-heading"><div><span class="eyebrow">DIRECTORY</span><h2>کاربران جدید</h2></div><a href="{{ route('admin.users.index') }}">مدیریت کاربران</a></div>
            @if(isset($recentUsers) && $recentUsers->count())<div class="user-list">@foreach($recentUsers as $user)<div class="user-row"><span class="user-avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span><div><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></div><i class="fas fa-chevron-left"></i></div>@endforeach</div>@else<div class="empty-state"><i class="fas fa-users"></i><p>کاربری یافت نشد.</p></div>@endif
        </div>
    </section>

    <section class="quick-actions"><div><span class="eyebrow">QUICK ACTIONS</span><h2>دسترسی سریع</h2></div><div class="action-links"><a href="{{ route('admin.users.create') }}"><i class="fas fa-user-plus"></i><span>کاربر جدید</span></a><a href="{{ route('admin.books.create') }}"><i class="fas fa-book-medical"></i><span>افزودن کتاب</span></a><a href="{{ route('admin.roles.index') }}"><i class="fas fa-shield-alt"></i><span>نقش‌ها و دسترسی‌ها</span></a></div></section>
</div>
@endsection