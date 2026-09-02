@extends('Layouts.admin')
@section('content')
<div class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm mx-auto md:max-w-2xl lg:max-w-4xl">
        @errors
        @success
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <form action="@route('admin.register.confirm')" method="post" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="mb-4 text-right">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="shop-name">نام مغازه</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="shop-name" type="text" name="name" />
                    </div>
                    <div class="mb-4 text-right">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="manage-name">نام مدیر صنف</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="manage-name" type="text" name="manage" />
                    </div>
                    <div class="mb-4 text-right">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="address">آدرس</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="address" type="text" name="address" />
                    </div>
                    <div class="mb-4 text-right col-span-1 md:col-span-3">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">توضیحات صنف</label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" name="desc" placeholder="توضیحات راجب صنف" rows="4"></textarea>
                    </div>
                    <div class="mb-4 relative text-right">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="category">انتخاب دسته بندی</label>
                        <div class="relative">
                            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="category" name="category">
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-2 text-gray-700">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 flex items-center justify-end col-span-1 md:col-span-3">
                        <label class="ml-2 text-gray-700">فعالیت در اینستا</label>
                        <input id="instagram-checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded" type="checkbox" onclick="toggleInstagramField()" />
                    </div>
                    <div id="instagram-field" class="mb-4 hidden text-right col-span-1 md:col-span-3">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="instagram-id">آیدی اینستاگرام</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="instagram-id" type="text" name="insta" />
                    </div>
                    <div class="mb-4 relative text-right col-span-1 md:col-span-3">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="">عکس صنف</label>
                        <div class="flex flex-col items-center relative mt-4">
                            <div id="dropArea" class="relative w-full h-52 md:w-full md:h-32 mb-4 rounded-lg border-2 border-dashed border-gray-400 flex items-center justify-center">
                                <label class="cursor-pointer flex flex-col items-center justify-center h-full">
                                    <i class="fas fa-camera text-gray-400 text-4xl"></i>
                                    <span class="text-gray-600 text-xs mt-1">عکس را بارگذاری کنید یا اینجا بکشید</span>
                                    <input accept="image/*" class="hidden" id="fileInputBackup" type="file" onchange="loadFile(event)" name="image" />
                                </label>
                                <img id="uploadedImage" class="hidden w-full h-full object-cover rounded-lg mt-2" alt="Uploaded Image" />
                                <button id="changeImageButton" class="hidden absolute right-4 bottom-4 bg-rose-400 hover:bg-rose-500 text-white rounded-lg p-2" type="button" onclick="changeImage()">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">ثبت</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function toggleInstagramField() {
        var checkbox = document.getElementById('instagram-checkbox');
        var instagramField = document.getElementById('instagram-field');
        if (checkbox.checked) {
            instagramField.classList.remove('hidden');
        } else {
            instagramField.classList.add('hidden');
        }
    }
</script>
<script>
    const dropArea = document.getElementById('dropArea');
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });
    dropArea.addEventListener('drop', handleDrop, false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight() {
        dropArea.classList.add('bg-gray-200');
    }

    function unhighlight() {
        dropArea.classList.remove('bg-gray-200');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            loadFile({
                target: {
                    files: files
                }
            });
        }
    }

    function loadFile(event) {
        const image = document.getElementById('uploadedImage');
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                image.src = e.target.result;
                image.classList.remove('hidden');
                document.getElementById('changeImageButton').classList.remove('hidden');
                const dropArea = document.getElementById('dropArea');
                dropArea.classList.remove('border-dashed', 'border-gray-400');
                dropArea.style.border = 'none';
                const label = dropArea.querySelector('label');
                label.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    function changeImage() {
        const fileInput = document.getElementById('fileInputBackup');
        fileInput.value = '';
        fileInput.click();
    }
</script>
<script src="http://pr.serv.wo/assets/js/sw.js"></script>
@endsection