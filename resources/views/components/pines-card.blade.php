@props(['title' => '', 'subtitle' => '', 'padding' => 'p-6'])

<div {{ $attributes->merge(['class' => 'rounded-xl backdrop-blur-xl transition-all duration-300 ' .
    'bg-white dark:bg-gray-800/90 ' .
    'border border-gray-200 dark:border-red-500/40 ' .
    'shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ' .
    'hover:bg-gray-50 dark:hover:bg-gray-800/95 hover:border-gray-300 dark:hover:border-red-500/60 ' . $padding]) }}>
    @if($title || $subtitle)
        <div class="border-b border-gray-200 dark:border-red-500/30 pb-4 mb-6">
            @if($title)
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    {{ $slot }}
</div>
