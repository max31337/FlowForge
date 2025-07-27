@props(['type' => 'success', 'title' => '', 'message' => '', 'duration' => 5000])

<div x-data="toast()" 
     x-show="show" 
     x-transition:enter="transform ease-out duration-300 transition"
     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed top-4 right-4 z-50 max-w-sm w-full pointer-events-auto"
     x-init="setTimeout(() => show = false, {{ $duration }})">
    
    <div class="relative rounded-lg shadow-2xl border backdrop-blur-xl overflow-hidden
                {{ $type === 'success' ? 'bg-black/90 border-red-500/30' : '' }}
                {{ $type === 'error' ? 'bg-black/90 border-red-600/50' : '' }}
                {{ $type === 'warning' ? 'bg-black/90 border-amber-500/30' : '' }}
                {{ $type === 'info' ? 'bg-black/90 border-blue-500/30' : '' }}">
        
        <!-- Glow Effect -->
        <div class="absolute inset-0 
                    {{ $type === 'success' ? 'bg-gradient-to-r from-red-500/5 to-transparent' : '' }}
                    {{ $type === 'error' ? 'bg-gradient-to-r from-red-600/10 to-transparent' : '' }}
                    {{ $type === 'warning' ? 'bg-gradient-to-r from-amber-500/5 to-transparent' : '' }}
                    {{ $type === 'info' ? 'bg-gradient-to-r from-blue-500/5 to-transparent' : '' }}"></div>
        
        <div class="relative p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    @if($type === 'success')
                        <div class="flex items-center justify-center w-8 h-8 bg-red-500/20 rounded-full">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    @elseif($type === 'error')
                        <div class="flex items-center justify-center w-8 h-8 bg-red-600/20 rounded-full">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    @elseif($type === 'warning')
                        <div class="flex items-center justify-center w-8 h-8 bg-amber-500/20 rounded-full">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                    @else
                        <div class="flex items-center justify-center w-8 h-8 bg-blue-500/20 rounded-full">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <div class="ml-3 flex-1">
                    @if($title)
                        <p class="text-sm font-semibold text-white">{{ $title }}</p>
                    @endif
                    <p class="text-sm text-gray-300 {{ $title ? 'mt-1' : '' }}">{{ $message }}</p>
                </div>
                
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="show = false" 
                            class="inline-flex text-gray-400 hover:text-white focus:outline-none focus:text-white transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-800">
            <div class="h-full transition-all duration-{{ $duration }} ease-linear
                        {{ $type === 'success' ? 'bg-red-500' : '' }}
                        {{ $type === 'error' ? 'bg-red-600' : '' }}
                        {{ $type === 'warning' ? 'bg-amber-500' : '' }}
                        {{ $type === 'info' ? 'bg-blue-500' : '' }}"
                 x-bind:style="`width: ${progress}%`"></div>
        </div>
    </div>
</div>

<script>
function toast() {
    return {
        show: true,
        progress: 100,
        init() {
            let duration = {{ $duration }};
            let interval = duration / 100;
            let timer = setInterval(() => {
                this.progress -= 1;
                if (this.progress <= 0) {
                    clearInterval(timer);
                }
            }, interval);
        }
    }
}
</script>
