@props([
    'href',
    'active' => false
])

<a href="{{ $href }}"
   {{ $attributes->merge([
       'class' => $active
            ? 'block rounded-lg bg-gray-100 px-4 py-2 font-semibold'
            : 'block rounded-lg px-4 py-2 hover:bg-gray-100'
   ]) }}>

    {{ $slot }}

</a>