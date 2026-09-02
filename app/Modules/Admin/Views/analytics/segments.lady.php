@extends('admin.layout')
@section('page_title','بخش‌بندی کاربران')
@section('content')
<div class="analytics-hero"><div><h1>👥 بخش‌بندی کاربران</h1><p>Segmentها توسط توسعه‌دهنده تعریف می‌شوند و می‌توانند منطق اختصاصی پروژه داشته باشند.</p></div><a class="btn btn-secondary" href="{{ route('admin.analytics.index') }}">Analytics</a></div>
<div class="analytics-kpis">@forelse($segments as $segment)<div class="metric-card"><span>{{ $segment['label'] }}</span><strong>{{ $segment['count'] }}</strong>@if($segment['description'])<small>{{ $segment['description'] }}</small>@endif</div>@empty<div class="empty-state">هنوز Segmentی تعریف نشده است.</div>@endforelse</div>
@endsection