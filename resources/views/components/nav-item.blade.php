@props([
    'route',
    'icon' => '',
])

@php
    $active = request()->routeIs($route);
@endphp

<a href="{{ route($route) }}"
    class="flex items-center gap-3 rounded-lg px-4 py-3 transition
    {{ $active ? 'bg-blue-600 text-black' : 'text-gray-700 hover:bg-gray-100' }}">

    @if($icon)

        <i class="{{ $icon }}"></i>

    @endif

    {{ $slot }}

</a>