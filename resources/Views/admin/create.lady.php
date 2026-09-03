@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
	<div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
		<h1 class="text-xl font-bold text-gray-800">ایجاد {{ $config['label'] }}</h1>
		<a href="{{ route('admin.' . $modelKey . '.index') }}" class="text-gray-600 hover:text-gray-900">بازگشت</a>
	</div>
	<form action="{{ route('admin.' . $modelKey . '.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
		@csrf
		@errors @enderrors
		<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
			@foreach($config['form'] as $field => $definition)
				@php
					$parts = explode('|', $definition);
					$type = $parts[0];
					$baseType = explode(':', $type, 2)[0];
					$required = in_array('required', $parts, true);
					$options = $baseType === 'select' ? explode(',', explode(':', $definition, 2)[1] ?? '') : [];
					$labels = ['name' => 'نام کامل', 'email' => 'ایمیل', 'password' => 'رمز عبور', 'role' => 'نقش', 'status' => 'وضعیت', 'slug' => 'شناسه', 'description' => 'توضیحات', 'priority' => 'اولویت', 'is_active' => 'وضعیت فعال', 'is_system' => 'نقش سیستمی', 'is_critical' => 'دسترسی بحرانی', 'title' => 'عنوان', 'author_name' => 'نام نویسنده', 'price' => 'قیمت', 'cover' => 'تصویر جلد'];
				@endphp
				<div class="{{ $baseType === 'textarea' ? 'md:col-span-2' : '' }}">
					<label for="{{ $field }}" class="block text-sm font-bold text-gray-700 mb-2">{{ $labels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}{{ $required ? ' *' : '' }}</label>
					@if($baseType === 'textarea')
						<textarea name="{{ $field }}" id="{{ $field }}" rows="4" class="w-full rounded-lg border-gray-300" {{ $required ? 'required' : '' }}>{{ old($field) }}</textarea>
					@elseif($baseType === 'select')
						<select name="{{ $field }}" id="{{ $field }}" class="w-full rounded-lg border-gray-300">
							@foreach($options as $option)<option value="{{ trim($option) }}">{{ ucfirst(trim($option)) }}</option>@endforeach
						</select>
					@elseif($baseType === 'checkbox')
						<input type="hidden" name="{{ $field }}" value="0"><label class="admin-checkbox"><input type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1"> فعال</label>
					@elseif($baseType === 'file')
						<input type="file" name="{{ $field }}" id="{{ $field }}" class="w-full rounded-lg border border-gray-300 p-2">
					@else
						<input type="{{ $baseType === 'password' ? 'password' : ($baseType === 'number' ? 'number' : ($baseType === 'email' ? 'email' : 'text')) }}" name="{{ $field }}" id="{{ $field }}" value="{{ old($field) }}" class="w-full rounded-lg border-gray-300" {{ $required ? 'required' : '' }}>
						@if(in_array('confirmed', $parts, true))<input type="password" name="{{ $field }}_confirmation" id="{{ $field }}_confirmation" placeholder="تکرار رمز عبور" class="w-full rounded-lg border-gray-300 mt-2" required>@endif
					@endif
				</div>
			@endforeach
		</div>
		<div class="mt-6 flex justify-end gap-3">
			<a href="{{ route('admin.' . $modelKey . '.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">انصراف</a>
			<button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700">ذخیره</button>
		</div>
	</form>
</div>
@endsection
