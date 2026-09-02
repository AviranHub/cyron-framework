@extends('admin.layout')
@section('page_title','مرکز نشست‌ها و امنیت کاربران')
@section('content')
<div class="analytics-hero"><div><h1>🛡️ مرکز نشست‌ها</h1><p>نمای فعال دستگاه‌ها، فعالیت اخیر و خروج اجباری کاربران</p></div></div>
<div class="stats-grid">
<div class="stat-card"><strong>{{ $summary['active'] }}</strong><span>نشست در این صفحه</span></div>
<div class="stat-card"><strong>{{ $summary['users'] }}</strong><span>کاربر یکتا</span></div>
<div class="stat-card"><strong>{{ $summary['stale'] }}</strong><span>نشست بدون فعالیت ۲۴ ساعت</span></div>
</div>
<form class="search-form" method="GET"><input name="user_id" value="{{ request()->input('user_id') }}" placeholder="شناسه کاربر"><button class="btn">جستجو</button></form>
<section class="card"><div class="card-body"><table class="table"><thead><tr><th>کاربر</th><th>IP</th><th>آخرین فعالیت</th><th>وضعیت</th><th>عملیات</th></tr></thead><tbody>
@foreach($sessions['data'] as $s)<tr><td>#{{ $s->user_id }}</td><td>{{ $s->ip_address ?? '-' }}</td><td>{{ $s->last_seen_at }}</td><td>🟢 فعال</td><td><form method="POST" action="{{ route('admin.security.sessions.revoke',['id'=>$s->id]) }}"><button class="btn btn-danger">خروج اجباری</button></form></td></tr>@endforeach
</tbody></table></div></section>
@endsection