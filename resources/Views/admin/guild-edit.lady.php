@extends('Layouts.admin')
@section('content')
<div class="py-6 sm:px-6 lg:px-8 w-full">
    <div class="container mx-auto">
        <div class="bg-gray-100 flex items-center justify-center min-h-screen">
            <div class="w-full max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg flex flex-col md:flex-row">
                <form action="{{ route('admin.guilds.edit.confirm') }}" method="post" enctype="multipart/form-data" class="w-full flex">
                    <input type="hidden" name="slug" value="{{ $guild->slug }}">
                    <!-- Sidebar -->
                    <div class="w-full md:w-1/4 border-l-0 md:border-l-2 md:pl-4 mb-4 md:mb-0 px-3 py-3">
                        <div class="flex flex-col items-center relative">
                            <img alt="تصویر صنف" class="w-full h-52 md:w-full md:h-32 mb-4 rounded-lg" id="profileImage" src="@storage('{{ $guild->image }}')" />
                            <label class="absolute bottom-4 left-0 bg-rose-600 text-white p-2 rounded-full cursor-pointer z-0" for="fileInput">
                                <i class="fas fa-edit"></i>
                            </label>
                            <input accept="image/*" class="hidden" id="fileInput" name="image" type="file" onchange="loadFile(event)" />
                        </div>
                        <h3 class="text-xl font-bold">پروفایل</h3>
                    </div>
                    <!-- Main Content -->
                    <div class="w-full md:w-3/4 md:pr-4">
                        <h2 class="text-2xl font-bold mb-6 text-center">ویرایش کسب و کار</h2>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="business-name">نام کسب و کار</label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="business-name" name="name" placeholder="نام کسب و کار خود را وارد کنید" type="text"
                                value="{{ $guild->name }}" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="business-bio">بیوگرافی یا توضیحات کسب و کار</label>
                            <textarea
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="business-bio" name="description" placeholder="بیوگرافی یا توضیحات کسب و کار خود را وارد کنید">{{ $guild->description }}</textarea>
                        </div>
                        <div class="mb-4 relative">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="business-category">دسته بندی کسب و کار</label>
                            <select name="category"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="business-category">
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $category->id == $guild->category ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="business-owner">صاحب کسب و کار</label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="business-owner" name="manage" placeholder="نام صاحب کسب و کار را وارد کنید" type="text" value="{{ $guild->manage }}" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="business-instagram">آیدی Instagram کسب و کار</label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="business-instagram" name="insta" placeholder="آیدی Instagram کسب و کار را وارد کنید" type="text" value="{{ $guild->insta }}" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="business-address">آدرس کسب و کار</label>
                            <textarea
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="business-address" name="address" placeholder="آدرس کسب و کار را وارد کنید">{{ $guild->address }}</textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <button
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="submit">
                                ذخیره تغییرات
                            </button>
                            <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
                                href="{{ route('admin.dashboard') }}">
                                لغو
                            </a>
                        </div>
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