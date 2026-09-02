@extends('Layouts.admin')

@section('content')
<div class="p-6 w-full text-center">
        @success
</div>

<div class="py-6 sm:px-6 lg:px-8 w-full">
    <div class="px-5 py-5 text-2xl font-bold">
        لیست اصناف
    </div>
    <div class="flex justify-between items-center mb-4 px-2 w-full">
        <div class="relative">
            <form action="" method="" class="">
                <input class="border border-gray-300 rounded-lg py-2 px-4 " placeholder="جستجو ..."
                    type="text" />
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline">
                    <i class="fa fa-search"></i>
                </button>
            </form>

        </div>
        <div class="flex items-center">
            <span class="ml-2">نمایش</span>
            <select class="border border-gray-300 rounded-lg py-2 px-4">
                <option>10</option>
                <option>20</option>
                <option>30</option>
            </select>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border-collapse rounded-lg shadow-md">
            <thead>
                <tr
                    class="bg-white text-black uppercase text-sm leading-normal border-b border-gray-200">
                    <th class="py-3 px-6 text-right">پروفایل</th>
                    <th class="py-3 px-6 text-right">عنوان</th>
                    <th class="py-3 px-6 text-right">صاحب صنف</th>
                    <th class="py-3 px-6 text-right">تاریخ انتشار</th>
                    <th class="py-3 px-6 text-right">وضعیت</th>
                    <th class="py-3 px-6 text-right">عملیات</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @foreach($guilds as $guild)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-right">
                        <img alt="تصویر صنف اول" class="w-16 h-16 rounded-lg"
                            src="@storage('{{$guild->image}}')" />
                    </td>
                    <td class="py-3 px-6 text-right">{{ $guild->name }}</td>
                    <td class="py-3 px-6 text-right">{{ $guild->manage }}</td>
                    <td class="py-3 px-6 text-right">
                        @php
                        $ex = explode(' ',$guild->created_at);
                        $date = explode('-',$ex[0]);
                        $jalali = gregorian_to_jalali($date[0],$date[1],$date[2]);
                        echo implode('/', $jalali);
                        @endphp
                    </td>
                    <td class="py-3 px-6 text-right">
                        @if($guild->status == 1)
                        <span
                            class="bg-yellow-200 text-yellow-600 py-1 px-3 rounded-full text-xs">در انتظار</span>

                        @endif
                        @if($guild->status == 0)
                        <span
                            class="bg-red-200 text-red-600 py-1 px-3 rounded-full text-xs">رد شده</span>

                        @endif
                        @if($guild->status == 2)
                        <span
                            class="bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs">منتشر
                            شده</span>

                        @endif
                    </td>
                    <td class="py-3 px-6 text-right">
                        <div class="flex item-center justify-center">
                            @if($guild->suggest == 1)
                            <a href="@route('admin.guilds.unsuggest',['slug' => '$guild->slug'])">
                                <div class="w-4 ml-4 transform hover:scale-110 text-yellow-500">
                                    <i class="fas fa-star"></i>
                                </div>
                            </a>
                            @else
                            <a href="@route('admin.guilds.suggest',['slug' => '$guild->slug'])">
                                <div class="w-4 ml-4 transform hover:scale-110 text-gray-500">
                                    <i class="fas fa-star"></i>
                                </div>
                            </a>
                            @endif

                            <a href="@route('admin.guilds.confirm',['slug' => '$guild->slug'])">
                                <div class="w-4 ml-4 transform hover:scale-110 text-green-500">
                                    <i class="fas fa-check"></i>
                                </div>
                            </a>
                            <a href="@route('admin.guilds.deny',['slug' => '$guild->slug'])">
                                <div class="w-4 ml-4 transform hover:scale-110 text-red-500">
                                    <i class="fas fa-close"></i>
                                </div>
                            </a>

                            <a href="@route('admin.guilds.edit',['slug' => '$guild->slug'])">
                                <div class="w-4 ml-4 transform hover:scale-110 text-indigo-500">
                                    <i class="fas fa-edit"></i>
                                </div>
                            </a>
                            <form class="delete-form" action="@route('admin.guilds.delete')" method="post">
                                <input type="hidden" name="id" value="{{$guild->id}}">
                                <button type="button" class="delete-button w-4 ml-4 transform hover:scale-110 text-red-500">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @endforeach
                <!-- سایر ردیف‌ها -->
            </tbody>
        </table>
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
                text: "این مغازه حذف خواهد شد.",
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