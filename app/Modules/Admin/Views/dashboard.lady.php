@extends('admin.layout')
@section('page_title','داشبورد')
@section('content')
<div class="stats">
<div class="stat-card"><div class="stat-label">کاربران</div><div class="stat-value">{{ $stats['users'] ?? 0 }}</div></div>
<div class="stat-card"><div class="stat-label">آیتم‌های امروز</div><div class="stat-value">{{ $stats['today'] ?? 0 }}</div></div>
<div class="stat-card"><div class="stat-label">فعالیت‌ها</div><div class="stat-value">{{ $stats['activities'] ?? 0 }}</div></div>
<div class="stat-card"><div class="stat-label">وضعیت سیستم</div><div class="stat-value">✓</div></div>
</div>
<div class="card"><div class="card-header"><strong>خوش آمدید</strong></div><div class="card-body">از منوی کناری برای مدیریت بخش‌های مختلف سیستم استفاده کنید.</div></div>
<div class="card"><div class="card-header"><strong>تحلیل رفتار کاربران</strong><a class="btn btn-secondary" href="{{ route('admin.activities.index') }}">مشاهده فعالیت‌ها</a></div><div class="card-body">فعالیت کاربران، ادمین‌ها و رویدادهای ثبت‌شده را برای تحلیل رفتار و تصمیم‌گیری بررسی کنید.</div></div>
@endsection