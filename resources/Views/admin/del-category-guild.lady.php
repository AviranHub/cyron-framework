@extends('Layouts.admin')
@section('content')
<div class="py-6 sm:px-6 lg:px-8 w-full">

    <!-- <div class="px-5 py-5 text-2xl font-bold">لیست اصناف</div> -->

    <div class="container mx-auto">
        @errors
        @success
        <div class="bg-gray-100 flex items-center justify-center min-h-screen">
            <div class="w-full max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6 text-center">
                    حذف دسته از اصناف
                </h2>
                <form action="@route('admin.guild.category.delete')" method="post">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="category-select">
                            انتخاب دسته
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category-select" name="id">
                            <option value="">
                                لطفا یک دسته را انتخاب کنید
                            </option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between">
                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                            حذف دسته
                        </button>
                        <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="@route('admin.dashboard')">
                            لغو
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
@section('script')
<script>
    var loadFile = function(event) {
        var image = document.getElementById('profileImage');
        image.src = URL.createObjectURL(event.target.files[0]);
    };
</script>
@endsection