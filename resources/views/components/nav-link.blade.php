@props(['active'])

@php
$classes = ($active ?? false)
? 'block px-4 py-2 text-sm font-semibold text-white bg-indigo-700 rounded-md transition duration-150 ease-in-out'
: 'block px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 rounded-md transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>