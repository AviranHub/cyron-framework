@extends('Layouts.app')

@section('content')
<div class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm mx-auto">
        <div class="flex justify-center mb-6">
            <img alt="Logo" class="h-12" height="50" src="https://storage.googleapis.com/a1aa/image/c8DCNkGfsWVifEpHoLeUE6hEPdl1KLlLiWU2SsZxglgOENLnA.jpg" width="50" />
        </div>
        <?= $msg = 1; ?>
        @isset($msg)
        <div class="flex justify-center mb-6">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">
                    خطا!
                </strong>
                <span class="block sm:inline">
                    {{ $msg }}
                </span>
            </div>
        </div>
        @endisset
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <form>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        ایمیل
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="username" id="email" type="email" placeholder="your@gmail.com" />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        پسورد
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="password" type="password" placeholder="password" />
                </div>
                <div class="mb-4 flex items-center">
                    <input checked class="form-checkbox h-5 w-5 text-green-500 rounded" name="saveme" type="checkbox" />
                    <label class="ml-2 text-gray-700">
                        منو یادت نره
                    </label>
                </div>
                <div class="flex items-center justify-between">
                    <a class="inline-block align-baseline font-bold text-sm text-gray-500 hover:text-gray-800" href="#">
                        رمز عبور خود را فراموش کردید؟
                    </a>
                    <button class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                        ورود
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection