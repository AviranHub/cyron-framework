@extends('admin.layout')
@section('page_title','گزارش ممیزی')
@section('content')
<div class="analytics-hero"><div><h1>🔐 گزارش ممیزی</h1><p>تغییرات مهم مدیریتی و امنیتی سیستم</p></div></div>
<form class="search-form audit-filters" method="GET"><input name="q" value="{{ request()->input('q') }}" placeholder="جستجو در عملیات و جزئیات"><input name="actor_id" value="{{ request()->input('actor_id') }}" placeholder="شناسه انجام‌دهنده"><input name="action" value="{{ request()->input('action') }}" placeholder="عملیات"><input type="date" name="from" value="{{ request()->input('from') }}"><input type="date" name="to" value="{{ request()->input('to') }}"><button class="btn">جستجو</button></form>
<section class="card"><div class="card-body"><table class="table"><thead><tr><th>زمان</th><th>انجام‌دهنده</th><th>عملیات</th><th>جزئیات</th></tr></thead><tbody>@foreach($logs as $log)<tr><td>{{ $log->occurred_at }}</td><td>#{{ $log->actor_id ?? '-' }}</td><td><a href="{{ route('admin.audit.show',['id'=>$log->id]) }}">{{ $log->action }}</a></td><td><details><summary>مشاهده</summary><code>{{ $log->context }}</code></details></td></tr>@endforeach</tbody></table></div></section>
@endsection