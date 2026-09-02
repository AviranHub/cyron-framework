@extends('Layouts.app')
@section('content')
<div class="bg-gray-100">
    <div class="max-w-7xl mx-auto p-4">
        <div class="p-4">
            <div class="flex">
                <form action="@route('search.results')" method="get" class="flex items-center gap-1 mb-4 w-full">
                    <input id="searchQuery" value="{{ isset($query) ? $query : '' }}" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-rose-500" placeholder="جستجو..." type="text" name="query" oninput="updateCategoryForms()" />
                    <button class="bg-rose-500 text-white rounded-md w-12 h-12 flex items-center justify-center" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            <div class="flex gap-3 mb-4 overflow-x-auto">
                @foreach($categories as $category)
                <form action="{{ route('search.results') }}" method="GET" class="inline-block">
                    <input type="hidden" name="query" value="" class="category-query"> <!-- اینجا را خالی می‌گذاریم -->
                    <input type="hidden" name="category" value="{{ $category->id }}">
                    <button type="submit" class="bg-white text-black border border-black px-4 py-2 rounded-full whitespace-nowrap">
                        {{ $category->name }}
                    </button>
                </form>
                @endforeach
            </div>
        </div>

        @if(!empty($guilds))
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($guilds as $guild)


            <div class="bg-white p-4 rounded-lg shadow-md">
                <img alt="{{ $guild->name }}" class="w-full h-48 object-cover rounded-t-lg" height="400" src="@storage('{{$guild->image}}')" width="600" />
                <div class="p-4">
                    <h2 class="text-xl font-bold mb-2">
                    {{ $guild->name }}
                    </h2>
                    <p class="text-gray-700">
                        {{ $guild->description }}
                    </p>
                    <a class="inline-block mt-4 px-6 py-2 text-white bg-blue-500 rounded-full hover:bg-blue-600" href="@route('guild',['slug' => '$guild->slug'])">
                        مشاهده مغازه
                    </a>
                </div>
            </div>
            
            @endforeach
        </div>

    @else
    <div class="text-center text-2xl font-bold text-gray-700 mt-4">{{$msg}}</div>
    @endif

</div>
</div>
@endsection
@section('script')
<script>
    function updateCategoryForms() {
        // دریافت مقدار از فرم جستجو
        var queryValue = document.getElementById('searchQuery').value;

        // به‌روزرسانی تمام فیلدهای ورودی مخفی در فرم‌های دسته‌بندی
        var categoryQueries = document.querySelectorAll('.category-query');
        categoryQueries.forEach(function(input) {
            input.value = queryValue; // تنظیم مقدار ورودی
        });
    }
</script>
@endsection