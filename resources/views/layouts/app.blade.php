<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
            (function(){
                const savedTheme = localStorage.getItem('theme');
                const systemPrefDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && systemPrefDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>
    </head>
    <body class="font-sans antialiased bg-white text-gray-900 dark:bg-dark-950 dark:text-white min-h-screen">
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
        
    <!-- Toast Container (top-right, above modals) -->
    <div id="toast-container" class="fixed top-4 right-4 z-[99999] space-y-2 pointer-events-none"></div>
        
        <!-- Livewire Scripts -->
        @livewireScripts
        
        <!-- Global Toast Scripts -->
        <script>
        // Unified global toast helper (compatible with both new and legacy call signatures)
        window.showToast = function(arg1 = 'success', arg2 = '', arg3 = '', arg4 = 5000) {
            // Support legacy: showToast(message, type)
            let type, title, message, duration;
            if (arg3 === '' && (arg2 === 'success' || arg2 === 'error' || arg2 === 'warning' || arg2 === 'info')) {
                message = String(arg1 || '');
                type = arg2 || 'success';
                title = '';
                duration = 5000;
            } else {
                type = arg1 || 'success';
                title = arg2 || '';
                message = arg3 || '';
                duration = Number(arg4) || 5000;
            }

            const container = document.getElementById('toast-container');
            if (!container) return;

            const wrapper = document.createElement('div');
            wrapper.className = 'pointer-events-auto';
            wrapper.innerHTML = `
                <div role="status" aria-live="polite" aria-atomic="true"
                    class="relative rounded-lg shadow-2xl border backdrop-blur-xl overflow-hidden bg-black/90 ${type === 'success' ? 'border-emerald-500/30' : type === 'error' ? 'border-red-600/50' : type === 'warning' ? 'border-amber-500/30' : 'border-blue-500/30'}">
                    <div class="absolute inset-0 ${type === 'success' ? 'bg-gradient-to-r from-emerald-500/5 to-transparent' : type === 'error' ? 'bg-gradient-to-r from-red-600/10 to-transparent' : type === 'warning' ? 'bg-gradient-to-r from-amber-500/5 to-transparent' : 'bg-gradient-to-r from-blue-500/5 to-transparent'}"></div>
                    <div class="relative p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-8 h-8 ${type === 'success' ? 'bg-emerald-500/20' : type === 'error' ? 'bg-red-600/20' : type === 'warning' ? 'bg-amber-500/20' : 'bg-blue-500/20'} rounded-full">
                                    <svg class="w-5 h-5 ${type === 'success' ? 'text-emerald-400' : type === 'error' ? 'text-red-400' : type === 'warning' ? 'text-amber-400' : 'text-blue-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />' : type === 'error' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />' : type === 'warning' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.268 16.5c-.77.833.192 2.5 1.732 2.5z" />' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'}
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                ${title ? `<p class="text-sm font-semibold text-white">${title}</p>` : ''}
                                <p class="text-sm text-gray-300 ${title ? 'mt-1' : ''}">${message}</p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button aria-label="Close notification" class="inline-flex text-gray-400 hover:text-white focus:outline-none focus:text-white transition-colors duration-200" data-close-btn>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-800">
                        <div class="h-full ${type === 'success' ? 'bg-emerald-500' : type === 'error' ? 'bg-red-600' : type === 'warning' ? 'bg-amber-500' : 'bg-blue-500'}" data-progress style="width: 100%;"></div>
                    </div>
                </div>`;

            container.appendChild(wrapper);
            const progress = wrapper.querySelector('[data-progress]');
            const toastEl = wrapper.firstElementChild;
            let start;
            function step(ts){
                if (start === undefined) start = ts;
                const elapsed = ts - start;
                const pct = Math.max(0, 100 - (elapsed / duration) * 100);
                if (progress) progress.style.width = pct + '%';
                if (elapsed < duration) {
                    requestAnimationFrame(step);
                } else {
                    if (toastEl) toastEl.style.opacity = '0';
                    setTimeout(() => wrapper.remove(), 150);
                }
            }
            requestAnimationFrame(step);

            const closeBtn = wrapper.querySelector('[data-close-btn]');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    if (toastEl) toastEl.style.opacity = '0';
                    setTimeout(() => wrapper.remove(), 150);
                });
            }
        };

    // Listen for new global 'toast' browser events
        window.addEventListener('toast', (e) => {
            const detail = e.detail || {};
            window.showToast(detail.type || 'info', detail.title || '', detail.message || '', detail.duration || 5000);
        });
    // Note: We intentionally avoid Livewire.on bridges here to prevent duplicate toasts.
        </script>
    </body>
</html>
