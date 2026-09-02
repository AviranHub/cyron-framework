<div class="alert alert-{{ $type ?? 'info' }} p-4 rounded-lg shadow mb-4 border-r-4 {{ $type === 'success' ? 'bg-green-100 border-green-500' : ($type === 'warning' ? 'bg-yellow-100 border-yellow-500' : 'bg-red-100 border-red-500') }}">
    <div class="font-bold text-lg mb-2">{{ $title ?? 'هشدار' }}</div>
    <div class="text-sm">{{ $slot }}</div>
    @if(isset($dismissible) && $dismissible)
        <button class="alert-close float-left text-gray-500 hover:text-gray-800">&times;</button>
    @endif
</div>