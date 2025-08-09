@props(['title' => '', 'subtitle' => '', 'embedded' => false])

<div class="{{ $embedded ? 'bg-transparent text-gray-900 dark:text-white' : 'min-h-screen bg-black text-white' }} relative overflow-hidden">
    <!-- Abstract Background Elements -->
    @unless($embedded)
        <x-abstract-bg variant="geometric1" position="top-right" />
        <x-abstract-bg variant="dots" position="bottom-left" />
        <x-abstract-bg variant="organic" position="center" />
    @endunless
    
    <!-- Main Content -->
    <div class="relative z-10">
    @if(($title || $subtitle) && !$embedded)
            <div class="bg-black/50 border-b border-red-500/20 backdrop-blur-xl">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    @if($title)
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $title }}</h1>
                    @endif
                    @if($subtitle)
                        <p class="text-gray-400">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
        @endif
        
    <main class="{{ $embedded ? 'px-4 sm:px-6 lg:px-8 py-6' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8' }}">
            {{ $slot }}
        </main>
    </div>
    
    <!-- Global Toast Container -->
    @unless($embedded)
        <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
    @endunless
</div>

<style>
/* Custom animations */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #1f1f1f;
}

::-webkit-scrollbar-thumb {
    background: #FF2D20;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #dc2626;
}
</style>
