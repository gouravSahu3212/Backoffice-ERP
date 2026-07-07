@props([
    'route',
    'icon' => '',
])

@php
    $active = request()->routeIs($route) || request()->routeIs($route . '.*');
@endphp

<a href="{{ route($route) }}"
    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
    {{ $active
        ? 'bg-gray-900 text-white'
        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

    @if(isset($icon))
        <span class="shrink-0 {{ $active ? 'text-white' : 'text-gray-400' }}">
            {{ $icon }}
        </span>
    @endif

    {{ $slot }}

</a>