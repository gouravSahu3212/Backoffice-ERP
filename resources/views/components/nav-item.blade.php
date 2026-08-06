@props([
    'route',
    'icon' => '',
    'icon2' => '',
    'badge' => 0,
])

@php
    $active = request()->routeIs($route) || request()->routeIs($route . '.*');
@endphp

<a href="{{ Route::has($route) ? route($route) : '#' }}"
    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
    {{ $active
        ? 'bg-gray-900 text-white'
        : 'text-gray-600 hover:bg-gray-200 hover:text-gray-900' }}">

    @if(isset($icon))
        <span class="shrink-0 {{ $active ? 'text-white' : 'text-gray-900' }}">
            {{ $icon }}
        </span>
    @endif

    <span x-show="sidebarOpen" x-transition class="ml-3 flex-1">
        {{ $slot }}
    </span>

    @if($badge > 0)
        <span x-show="sidebarOpen" x-transition
            class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[11px] font-bold rounded-full
            {{ $active ? 'bg-white text-gray-900' : 'bg-red-500 text-white' }}">
            {{ $badge > 99 ? '99+' : $badge }}
        </span>
    @endif

    @if(isset($icon2))
        <span x-show="sidebarOpen" x-transition class="ml-3">
            {{ $icon2 }}
        </span>
    @endif

</a>