<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Page Not Found</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Fallback styles */
                body { font-family: Figtree, ui-sans-serif, system-ui, sans-serif; }
                .bg-black { background-color: #000000; }
                .text-white { color: #ffffff; }
                .bg-zinc-900 { background-color: #18181b; }
                .ring-zinc-800 { --tw-ring-color: #27272a; }
            </style>
        @endif
        
        <!-- Theme initialization script -->
        <script>
            (function() {
                const savedTheme = localStorage.getItem('theme');
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
            <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                    <main class="mt-6">
                        <div class="flex flex-col items-center">
                            <!-- 404 Error Card -->
                            <div class="flex flex-col items-center gap-6 rounded-lg bg-white p-8 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:bg-zinc-900 dark:ring-zinc-800 lg:p-12">
                                <div class="flex size-16 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10">
                                    <svg class="size-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke="#FF2D20" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>

                                <div class="text-center">
                                    <h1 class="text-6xl font-bold text-[#FF2D20] mb-4">404</h1>
                                    <h2 class="text-2xl font-semibold text-black dark:text-white mb-4">Page Not Found</h2>
                                    <p class="text-gray-600 dark:text-gray-300 mb-8 max-w-md">
                                        The page you're looking for doesn't exist or has been moved to another location.
                                    </p>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a
                                        href="{{ url()->previous() }}"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-[#FF2D20] hover:bg-[#FF2D20]/90 text-white font-semibold rounded-md transition duration-150 ease-in-out"
                                    >
                                        <svg class="size-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                        Go Back
                                    </a>
                                    <a
                                        href="{{ url('/') }}"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-gray-300 font-semibold rounded-md hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-150 ease-in-out"
                                    >
                                        <svg class="size-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        Go Home
                                    </a>
                                </div>
                            </div>
                        </div>
                    </main>

                    <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                        @if(tenancy()->initialized && tenancy()->tenant)
                            {{ tenancy()->tenant->name }} • FlowForge Platform
                        @else
                            FlowForge Platform
                        @endif
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>
