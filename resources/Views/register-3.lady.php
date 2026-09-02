@extends('Layouts.app')
@section('content')
<div class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm mx-auto">
        <div class="flex justify-center mb-6">
            <img alt="Logo" class="h-12" height="50" src="<?php asset('/img/logo.png'); ?>" width="50" />
        </div>
        @errors()
        @success()
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <form action="@route('register')" method="post" enctype="multipart/form-data">

                <div class="mb-4 text-right">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="shop-name">
                        نام مغازه
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="shop-name" type="text" name="name" />
                </div>
                <div class="mb-4 text-right">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        توضیحات صنف
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" name="desc" placeholder="توضیحات راجب صنف" rows="4"></textarea>
                </div>
                <div class="mb-4 text-right">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="manage-name">
                        نام مدیر صنف
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="manage-name" type="text" name="manage" />
                </div>
                <div class="mb-4 text-right">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="address">
                        آدرس
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="address" type="text" name="address" />
                </div>
                <div class="mb-4 relative text-right">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">
                        انتخاب دسته بندی
                    </label>
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
                <div class="mb-4 flex items-center justify-end">
                    <label class="ml-2 text-gray-700">
                        فعالیت در اینستا
                    </label>
                    <input id="instagram-checkbox" class="form-checkbox h-5 w-5 text-green-500 rounded" type="checkbox" onclick="toggleInstagramField()" />
                </div>
                <div id="instagram-field" class="mb-4 hidden text-right">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="instagram-id">
                        آیدی اینستاگرام
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="instagram-id" type="text" name="insta" />
                </div>

                <div class="mb-4 relative text-right">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="">
                        عکس صنف
                    </label>
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








                <div class="flex items-center justify-between">
                    <button class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        ثبت
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('script')
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

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    // Highlight drop area when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    // Unhighlight drop area when item is no longer dragged over it
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    // Handle dropped files
    dropArea.addEventListener('drop', handleDrop, false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight() {
        dropArea.classList.add('bg-gray-200'); // Change background color when highlighted
    }

    function unhighlight() {
        dropArea.classList.remove('bg-gray-200'); // Reset background color
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            loadFile({
                target: {
                    files: files
                }
            }); // Use the existing loadFile function
        }
    }

    function loadFile(event) {
        const image = document.getElementById('uploadedImage');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                image.src = e.target.result; // Set the source of the image to the uploaded file
                image.classList.remove('hidden'); // Show the image
                document.getElementById('changeImageButton').classList.remove('hidden'); // Show the change image button

                // Hide the label and remove border
                const dropArea = document.getElementById('dropArea');
                dropArea.classList.remove('border-dashed', 'border-gray-400'); // Remove border classes
                dropArea.style.border = 'none'; // Remove border style
                const label = dropArea.querySelector('label');
                label.classList.add('hidden'); // Hide the label
            }
            reader.readAsDataURL(file); // Read the file as a data URL
        }
    }


    function changeImage() {
        const fileInput = document.getElementById('fileInputBackup');
        fileInput.value = ''; // Reset the file input
        fileInput.click(); // Trigger click on the file input
    }
</script>
@endsection