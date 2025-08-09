@props(['show' => false, 'maxWidth' => 'md'])

@php
$maxWidth = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
][$maxWidth];
@endphp

<div x-data="{ show: @js($show) }" 
     x-show="show" 
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm"
         @click="show = false">
    </div>

    <!-- Modal Content -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="w-full {{ $maxWidth }} bg-dark-900 rounded-xl shadow-2xl border border-gray-800 overflow-hidden">
            
            {{ $slot }}
        </div>
    </div>
</div>
