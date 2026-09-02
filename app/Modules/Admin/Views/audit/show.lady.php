@extends('admin.layout')
@section('page_title','جزئیات گزارش ممیزی')
@section('content')
<div class="analytics-hero"><div><h1>🔐 {{ $log->action }}</h1><p>جزئیات کامل عملیات ثبت‌شده</p></div><a class="btn btn-secondary" href="{{ route('admin.audit.index') }}">بازگشت</a></div>
<section class="card"><div class="event-stat"><span>زمان</span><b>{{ $log->occurred_at }}</b></div><div class="event-stat"><span>انجام‌دهنده</span><b>#{{ $log->actor_id ?? '-' }}</b></div><div class="event-stat"><span>عملیات</span><b>{{ $log->action }}</b></div></section>
@if(isset($context['before']) || isset($context['after']))
<section class="card"><div class="card-header"><strong>📝 تغییرات</strong><span>قبل ← بعد</span></div><div class="card-body"><table class="table change-table"><thead><tr><th>فیلد</th><th>قبل</th><th>بعد</th></tr></thead><tbody>@foreach(array_unique(array_merge(array_keys($context['before'] ?? []),array_keys($context['after'] ?? []))) as $field)@php($before=$context['before'][$field] ?? null)@php($after=$context['after'][$field] ?? null)@if(json_encode($before)!==json_encode($after))<tr><td><code>{{ $field }}</code></td><td>{{ is_array($before)?json_encode($before,JSON_UNESCAPED_UNICODE):($before ?? '-') }}</td><td>{{ is_array($after)?json_encode($after,JSON_UNESCAPED_UNICODE):($after ?? '-') }}</td></tr>@endif@endforeach</tbody></table></div></section>
@endif
<section class="card"><div class="card-header"><strong>Context کامل</strong></div><pre class="audit-context">{{ json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></section>
@endsection