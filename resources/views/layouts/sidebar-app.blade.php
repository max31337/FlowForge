<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data="{}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FlowForge') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    <!-- Alpine for theme toggle if not already included -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
<body class="h-full bg-gray-50 dark:bg-zinc-950 text-gray-900 dark:text-zinc-100">
    <!-- Sidebar -->
    <div x-data="{ sidebarOpen: false }" class="h-full">
        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" x-description="Off-canvas menu for mobile, show/hide based on off-canvas menu state.">
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80"></div>
            
            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative mr-16 flex w-full max-w-xs flex-1">
                    <div x-show="sidebarOpen" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Mobile sidebar content -->
                    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-zinc-900 px-6 pb-2">
                        <div class="flex h-16 shrink-0 items-center">
                            <h1 class="text-xl font-bold text-blue-600">FlowForge</h1>
                        </div>
                        @include('layouts.partials.sidebar-nav')
                    </div>
                </div>
            </div>
        </div>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-6">
                <div class="flex h-16 shrink-0 items-center">
                    <h1 class="text-xl font-bold text-blue-600">FlowForge</h1>
                </div>
                @include('layouts.partials.sidebar-nav')
            </div>
        </div>

        <!-- Main content area -->
        <div class="lg:pl-72">
            <!-- Top navigation bar -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8 transition-colors">
                <button type="button" class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-300 lg:hidden" @click="sidebarOpen = true">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-200 lg:hidden"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1">
                        <!-- Search can go here if needed -->
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Theme Toggle -->
                        <x-theme-toggle />
                        <!-- Notifications -->
                        <button type="button" class="-m-2.5 p-2.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </button>

                        <!-- Profile dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button type="button" class="-m-1.5 flex items-center p-1.5" @click="open = !open">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-zinc-700 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700 dark:text-zinc-100">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900 dark:text-zinc-100">{{ Auth::user()->name }}</span>
                                    <svg class="ml-2 h-5 w-5 text-gray-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2.5 w-40 origin-top-right rounded-md bg-white dark:bg-zinc-800 py-2 shadow-lg ring-1 ring-gray-900/5 dark:ring-zinc-700">
                                <a href="{{ route('profile.edit') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-zinc-100 hover:bg-gray-50 dark:hover:bg-zinc-700/60">Your profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-3 py-1 text-sm leading-6 text-gray-900 dark:text-zinc-100 hover:bg-gray-50 dark:hover:bg-zinc-700/60">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Optional Breeze-style header slot -->
            @isset($header)
                <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800">
                    <div class="px-4 sm:px-6 lg:px-8 py-4">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <!-- Main content -->
            <main class="py-8">
                <div class="px-4 sm:px-6 lg:px-8">
                    @yield('content')
                    {{-- Support for components using $slot --}}
                    {{ $slot ?? '' }}
                </div>
            </main>
    </div>

    <!-- Global Toast Outlet (upper-right, above modals) -->
    <div id="toast-container" class="fixed top-4 right-4 z-[99999] space-y-2 pointer-events-none"></div>
    </div>

    @livewireScripts
    <script>
    // Helper to show toasts globally from Livewire or JS
    window.showToast = function(type = 'success', title = '', message = '', duration = 5000) {
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
        // Animate progress bar and handle auto-dismiss
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
                // fade out then remove
                if (toastEl) toastEl.style.opacity = '0';
                setTimeout(() => wrapper.remove(), 150);
            }
        }
        requestAnimationFrame(step);

        // Manual close
        const closeBtn = wrapper.querySelector('[data-close-btn]');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                if (toastEl) toastEl.style.opacity = '0';
                setTimeout(() => wrapper.remove(), 150);
            });
        }
    };

    // Bridge Livewire browser events -> global toast helper
    window.addEventListener('toast', (e) => {
        const detail = e.detail || {};
        window.showToast(detail.type || 'info', detail.title || '', detail.message || '', detail.duration || 5000);
    });

    // Note: We intentionally avoid Livewire.on('toast') to prevent double toasts.

    // Allow views/components to request closing all active toasts before opening modals
    window.addEventListener('close-toasts', () => {
        const container = document.getElementById('toast-container');
        if (!container) return;
        container.querySelectorAll(':scope > div').forEach(el => el.remove());
    });
    </script>
    @stack('scripts')
</body>
</html>