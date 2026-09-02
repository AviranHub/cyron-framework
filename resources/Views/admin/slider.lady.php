@extends('Layouts.admin')

@section('content')
<div class="container mx-auto p-4">
    @errors
    @success
    <h1 class="text-2xl font-bold mb-4">
        مدیریت اسلایدر
    </h1>
    <!-- Form to add new image -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <h2 class="text-xl font-semibold mb-4">
            افزودن تصویر جدید
        </h2>
        <form action="@route('admin.setting.slider.add')" method="post" id="addImageForm" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700" for="imageFile">
                    انتخاب تصویر:
                </label>
                <input accept="image/*" class="w-full p-2 border border-gray-300 rounded mt-1" id="imageFile" name="image" type="file" />
            </div>
            <div class="mb-4">
                <label class="block text-gray-700" for="imageAlt">
                    توضیحات تصویر:
                </label>
                <input class="w-full p-2 border border-gray-300 rounded mt-1" id="imageAlt" name="desc" placeholder="توضیحات تصویر" type="text" />
            </div>
            <button class="bg-blue-500 text-white px-4 py-2 rounded" type="submit">
                افزودن
            </button>
        </form>
    </div>
    <!-- List of images -->
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">
            تصاویر اسلایدر
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="sliderImages">
            <!-- Example image item -->
            @foreach($sliders as $slider)
            <div class="relative">
                <img alt="{{ $slider->description }}" class="w-full h-48 object-cover rounded" height="200" src="@storage('{{$slider->image}}')" width="300" />
                <div class="absolute top-2 right-2 flex space-x-2">
                    <form action="@route('admin.setting.slider.delete')" method="post" class="delete-form">
                        <input type="hidden" name="id" value="{{$slider->id}}">
                        <button class="bg-red-500 text-white p-2 rounded delete-button">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // جلوگیری از ارسال فرم به طور پیش‌فرض

            const form = this.closest('.delete-form'); // پیدا کردن فرم مربوطه

            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: "این تصویر حذف خواهد شد.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله، حذف کن!',
                cancelButtonText: 'خیر، انصراف!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // ارسال فرم در صورت تأیید
                }
            });
        });
    });
</script>
@endsection