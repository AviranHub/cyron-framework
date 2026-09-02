@extends('admin.layout')
@section('page_title','تاریخچه ورود')
@section('content')
<div class="analytics-hero"><div><h1>🔐 تاریخچه ورود</h1><p>ورودهای موفق و ناموفق کاربران</p></div></div>
<form class="search-form audit-filters" method="GET"><input name="user_id" placeholder="شناسه کاربر"><select name="status"><option value="">همه</option><option value="1">موفق</option><option value="0">ناموفق</option></select><input type="date" name="from"><input type="date" name="to"><button class="btn">فیلتر</button></form>
<section class="card"><div class="card-body"><table class="table"><thead><tr><th>زمان</th><th>کاربر</th><th>وضعیت</th><th>IP</th><th>User Agent</th></tr></thead><tbody>@foreach($logs as $log)<tr><td>{{ $log->occurred_at }}</td><td>#{{ $log->user_id ?? '-' }}</td><td>{{ $log->successful ? 'موفق' : 'ناموفق' }}</td><td>{{ $log->ip_address ?? '-' }}</td><td>{{ $log->user_agent ?? '-' }}</td></tr>@endforeach</tbody></table></div></section>
@endsection