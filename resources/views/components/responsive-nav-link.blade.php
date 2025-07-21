@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#FF2D20] dark:border-[#FF2D20] text-start text-base font-medium text-[#FF2D20] dark:text-[#FF2D20] bg-[#FF2D20]/10 dark:bg-[#FF2D20]/10 focus:outline-none focus:text-[#FF2D20] dark:focus:text-[#FF2D20] focus:bg-[#FF2D20]/20 dark:focus:bg-[#FF2D20]/20 focus:border-[#FF2D20] dark:focus:border-[#FF2D20] transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-white/70 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-zinc-800 hover:border-gray-300 dark:hover:border-zinc-700 focus:outline-none focus:text-gray-800 dark:focus:text-white focus:bg-gray-50 dark:focus:bg-zinc-800 focus:border-gray-300 dark:focus:border-zinc-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
