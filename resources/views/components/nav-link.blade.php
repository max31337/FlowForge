@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#FF2D20] dark:border-[#FF2D20] text-sm font-medium leading-5 text-gray-900 dark:text-white focus:outline-none focus:border-[#FF2D20] dark:focus:border-[#FF2D20] transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-white/70 hover:text-gray-700 dark:hover:text-white hover:border-gray-300 dark:hover:border-zinc-700 focus:outline-none focus:text-gray-700 dark:focus:text-white focus:border-gray-300 dark:focus:border-zinc-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
