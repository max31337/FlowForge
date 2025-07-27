@props(['title' => '', 'subtitle' => '', 'padding' => 'p-6'])

<div {{ $attributes->merge(['class' => 'bg-gray-800/90 backdrop-blur-xl border border-red-500/40 rounded-xl shadow-2xl hover:border-red-500/60 hover:bg-gray-800/95 transition-all duration-300 ' . $padding]) }}>
    @if($title || $subtitle)
        <div class="border-b border-red-500/30 pb-4 mb-6">
            @if($title)
                <h3 class="text-lg font-semibold text-white">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-gray-300 text-sm mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    {{ $slot }}
</div>
