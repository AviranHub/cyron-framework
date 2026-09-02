@extends('admin.layout')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>{{ $config['label'] }}</h3>
        <a href="{{ route('admin.' . $modelKey . '.create') }}" class="btn">جدید</a>
    </div>
    <div class="card-body">
        <form method="GET" class="search-form">
@if($modelKey === 'users')<select name="status" class="status-filter"><option value="">همه وضعیت‌ها</option><option value="active">فعال</option><option value="inactive">غیرفعال</option><option value="suspended">معلق</option></select>@endif
            <input type="text" name="search" placeholder="جستجو..." value="{{ input('search') }}">
            <button type="submit">جستجو</button>
        </form>
        <table class="table">
            <thead>
                <tr>
                    @foreach($config['columns'] as $col)
                        <th>{{ $col }}</th>
                    @endforeach
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items['data'] as $item)
                <tr>
                    @foreach($config['columns'] as $col)
                        <td>{{ $item->{$col} }}</td>
                    @endforeach
                    <td>
                        <a class="btn btn-secondary" href="{{ route('admin.' . $modelKey . '.edit', $item->id) }}">ویرایش</a>
                        <form action="{{ route('admin.' . $modelKey . '.destroy', $item->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('حذف شود؟')">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ paginate_links($items) }}
    </div>
</div>
@endsection