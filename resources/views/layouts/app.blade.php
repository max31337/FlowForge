<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FlowForge') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Livewire Styles -->
        @livewireStyles
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Theme initialization script -->
        <script>
            document.documentElement.classList.add('dark');
        </script>
    </head>
    <body class="font-sans antialiased bg-dark-950 text-white min-h-screen">
        <!-- Abstract Background Elements -->
        <x-abstract-bg variant="geometric1" position="top-right" />
        <x-abstract-bg variant="organic" position="bottom-left" />
        <x-abstract-bg variant="dots" position="center" />
        
        <div class="relative min-h-screen selection:bg-accent-500 selection:text-white">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-dark-900/50 backdrop-blur-sm border-b border-gray-800 shadow-lg">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between">
                            {{ $header }}
                            @if(tenancy()->initialized)
                                <div class="flex items-center space-x-2 text-sm text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h6m-6 4h6m-6 4h6"/>
                                    </svg>
                                    <span>{{ tenancy()->tenant->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="relative z-10">
                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
        
        <!-- Toast Container -->
        <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
            <!-- Toast notifications will be inserted here -->
        </div>
        
        <!-- Livewire Scripts -->
        @livewireScripts
        
        <!-- Custom Scripts -->
        <script>
            // Show toast notification
            function showToast(message, type = 'success') {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.innerHTML = `
                    <div class="toast-${type} animate-slide-up" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'}
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium">${message}</div>
                            </div>
                            <div class="flex-shrink-0">
                                <button onclick="this.closest('.toast-${type}').remove()" class="text-gray-400 hover:text-gray-200 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(toast);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 5000);
            }

            // Listen for Livewire events
            document.addEventListener('livewire:init', () => {
                Livewire.on('show-toast', (data) => {
                    showToast(data.message, data.type || 'success');
                });
            });
        </script>
    </body>
</html>
