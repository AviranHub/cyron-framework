@extends('admin.layout')


@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">ویرایش {{ $config['label'] }}</h2>
        <a href="{{ route('admin.' . $modelKey . '.index') }}" class="text-gray-600 hover:text-gray-800">← بازگشت</a>
    </div>

    {{-- نمایش خطاهای عمومی --}}
    @errors @enderrors

    <form action="{{ route('admin.' . $modelKey . '.update', ['id' => $item->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @foreach($config['form'] as $field => $definition)
            @php
                $parts = explode('|', $definition);
                $type = $parts[0];
                $options = [];
                $isSelect = ($type === 'select');
                if ($isSelect) {
                    $selectOptions = explode(':', $definition, 2);
                    $options = explode(',', $selectOptions[1] ?? '');
                }
                $isFile = ($type === 'file');
                $value = $item->{$field} ?? '';
            @endphp

            <div class="mb-4">
                <label for="{{ $field }}" class="block text-gray-700 text-sm font-bold mb-2">
                    {{ ucfirst(str_replace('_', ' ', $field)) }}
                </label>

                @if($type === 'textarea')
                    <textarea name="{{ $field }}" id="{{ $field }}" rows="4"
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old($field, $value) }}</textarea>

                @elseif($type === 'select')
                    <select name="{{ $field }}" id="{{ $field }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @foreach($options as $opt)
                            <option value="{{ trim($opt) }}" {{ old($field, $value) == trim($opt) ? 'selected' : '' }}>
                                {{ ucfirst(trim($opt)) }}
                            </option>
                        @endforeach
                    </select>

                @elseif($type === 'file')
                    @if($value)
                        <div class="mb-2">
                            <img src="{{ storage_url($value) }}" class="h-20 w-20 object-cover rounded">
                        </div>
                    @endif
                    <input type="file" name="{{ $field }}" id="{{ $field }}"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">

                @elseif($type === 'password')
                    <input type="password" name="{{ $field }}" id="{{ $field }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-xs text-gray-500 mt-1">برای تغییر رمز عبور، مقدار جدید وارد کنید. در غیر این صورت خالی بگذارید.</p>

                @else
                    <input type="{{ $type === 'number' ? 'number' : 'text' }}" name="{{ $field }}" id="{{ $field }}"
                           value="{{ old($field, $value) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @endif

                @error("$field")
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <div class="flex items-center justify-end gap-2">
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                ذخیره تغییرات
            </button>
            <a href="{{ route('admin.' . $modelKey . '.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                انصراف
            </a>
        </div>
    </form>
</div>
@endsection