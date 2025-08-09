@props(['variant' => 'primary', 'size' => 'md', 'icon' => '', 'loading' => false])

@php
$variants = [
    'primary' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white border-red-600 shadow-lg shadow-red-600/25',
    'secondary' => 'bg-gray-100 hover:bg-gray-200 text-gray-900 border-gray-300 focus:ring-red-500 dark:bg-gray-700/80 dark:hover:bg-gray-600/90 dark:text-white dark:border-red-500/40 dark:hover:border-red-500/60',
    'outline' => 'bg-transparent hover:bg-red-50 text-red-700 border-red-300 hover:border-red-400 focus:ring-red-500 dark:hover:bg-red-600/20 dark:text-red-400 dark:border-red-500/60 dark:hover:border-red-500',
    'ghost' => 'bg-transparent hover:bg-gray-100 text-gray-700 border-transparent hover:border-gray-200 focus:ring-red-500 dark:hover:bg-red-600/10 dark:text-red-400 dark:hover:border-red-500/30',
    'danger' => 'bg-red-700 hover:bg-red-800 focus:ring-red-600 text-white border-red-700 shadow-lg shadow-red-700/25',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
    'xl' => 'px-8 py-4 text-lg',
];
@endphp

<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center justify-center font-medium rounded-lg border backdrop-blur-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-black disabled:opacity-50 disabled:cursor-not-allowed ' . 
    ($variants[$variant] ?? $variants['primary']) . ' ' . 
    ($sizes[$size] ?? $sizes['md'])
]) }}>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Loading...
    @else
        @if($icon)
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        @endif
        {{ $slot }}
    @endif
</button>
