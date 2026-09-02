@extends('admin.layout')
@section('page_title','فعالیت کاربران')
@section('content')
<div class="rbac-header"><div><h1>فعالیت کاربران</h1><p>بررسی رفتار کاربران، ادمین‌ها و رویدادهای سیستم</p></div></div>
<form class="search-form activity-filters" method="GET"><input name="user_id" placeholder="شناسه کاربر" value="{{ request()->input('user_id') }}"><input name="category" placeholder="دسته فعالیت"><input name="action" placeholder="عملیات"><input type="date" name="from"><input type="date" name="to"><button class="btn">فیلتر</button></form>
<div class="card"><div class="card-body"><table class="table"><thead><tr><th>زمان</th><th>کاربر</th><th>دسته</th><th>عملیات</th><th>مبلغ/ارزش</th><th>دستگاه</th></tr></thead><tbody>
@foreach($activities as $activity)<tr><td>{{ $activity->occurred_at }}</td><td><a href="{{ route('admin.activities.user',$activity->user_id) }}">#{{ $activity->user_id }}</a></td><td>{{ $activity->category }}</td><td>{{ $activity->action }}</td><td>{{ $activity->amount ?? '-' }}</td><td>{{ $activity->device ?? '-' }}</td></tr>@endforeach
</tbody></table></div></div>
@endsection