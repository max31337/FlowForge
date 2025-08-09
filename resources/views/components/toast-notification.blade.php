@props(['type' => 'success', 'dismissible' => true])

@php
$types = [
    'success' => 'toast-success',
    'error' => 'toast-error',
    'warning' => 'bg-yellow-900/20 border-yellow-600',
    'info' => 'bg-blue-900/20 border-blue-600',
];

$icons = [
    'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
    'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>',
    'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
];
@endphp

<div x-data="{ 
        show: true, 
        type: '{{ $type }}',
        init() {
            if (this.show) {
                setTimeout(() => this.show = false, 5000);
            }
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="translate-x-full opacity-0"
    x-transition:enter-end="translate-x-0 opacity-100"
    x-transition:leave="transition ease-in duration-300 transform"
    x-transition:leave-start="translate-x-0 opacity-100"
    x-transition:leave-end="translate-x-full opacity-0"
    class="{{ $types[$type] ?? $types['info'] }}">
    
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icons[$type] ?? $icons['info'] !!}
            </svg>
        </div>
        
        <div class="flex-1 min-w-0">
            <div class="text-sm font-medium">
                {{ $slot }}
            </div>
        </div>
        
        @if($dismissible)
            <div class="flex-shrink-0">
                <button @click="show = false" 
                        class="text-gray-400 hover:text-gray-200 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
    </div>
</div>
