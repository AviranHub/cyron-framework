@extends('admin.layout')
@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ $config['label'] }}</h3>
        <a href="{{ route('admin.' . $modelKey . '.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 ease-in-out shadow-sm">جدید +</a>
    </div>
    <div class="p-6">
        <form method="GET" class="mb-6 flex gap-2">
            <input class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="search" placeholder="جستجو..." value="{{ input('search') }}">
            <button class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-5 rounded-lg transition duration-200" type="submit">جستجو</button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($config['columns'] as $col)
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $col }}</th>
                        @endforeach
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($items['data'] as $item)
                    <tr>
                        @foreach($config['columns'] as $col)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->{$col} }}</td>
                        @endforeach
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                            <a class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition" href="{{ route('admin.' . $modelKey . '.edit', ['id' => $item->id]) }}">ویرایش</a>
                            <form action="{{ route('admin.' . $modelKey . '.destroy', ['id' => $item->id]) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition" type="submit" onclick="return confirm('حذف شود؟')">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="mt-6">
            {!! paginate_links($items) !!}
        </div>
    </div>
</div>
@endsection