@props([
    'route',
    'icon' => '',
    'icon2' => '',
])

@php
    $active = request()->routeIs($route) || request()->routeIs($route . '.*');
@endphp

<a href="{{ route($route) }}"
    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 transition
    {{ $active
        ? 'bg-gray-900 text-white'
        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

    @if(isset($icon))
        <span class="shrink-0 {{ $active ? 'text-white' : 'text-gray-900' }}">
            {{ $icon }}
        </span>
    @endif

    <span x-show="sidebarOpen" x-transition class="ml-3">
        {{ $slot }}
    </span>

    @if(isset($icon2))
        <span x-show="sidebarOpen" x-transition class="ml-3">
            {{ $icon2 }}
        </span>
    @endif

</a>