<nav x-data="{ open: false }" class="bg-white/70 dark:bg-dark-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 shadow-lg relative z-20 transition-colors">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::check() ? dashboard_route() : url('/') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-accent-500 to-accent-700 rounded-lg flex items-center justify-center shadow-glow">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-500 to-orange-400 dark:from-red-400 dark:to-orange-300">FlowForge</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                @auth
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ dashboard_route() }}" class="nav-link {{ request()->routeIs(dashboard_route_name()) ? 'nav-link-active' : '' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        </svg>
                        {{ __('Dashboard') }}
                    </a>
                    
                    @if(tenancy()->initialized)
                        @can('read_projects')
                            <a href="{{ route('tenant.projects') }}" class="nav-link {{ request()->routeIs('tenant.projects*') ? 'nav-link-active' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                {{ __('Projects') }}
                            </a>
                        @endcan
                        
                        @can('read_tasks')
                            <a href="{{ route('tenant.tasks') }}" class="nav-link {{ request()->routeIs('tenant.tasks*') ? 'nav-link-active' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                {{ __('Tasks') }}
                            </a>
                        @endcan
                        
                        @can('manage_users')
                            <a href="{{ route('tenant.users.index') }}" class="nav-link {{ request()->routeIs('tenant.users*') ? 'nav-link-active' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                {{ __('Users') }}
                            </a>
                        @endcan
                    @endif
                </div>
                @endauth
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <x-theme-toggle />
                @guest
                    <!-- Guest Navigation -->
                    <a href="{{ route('login') }}" class="btn-ghost">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        {{ __('Log in') }}
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            {{ __('Register') }}
                        </a>
                    @endif
                @else
                    <!-- User Avatar and Dropdown -->
                    <x-dropdown align="right" width="64">
                        <x-slot name="trigger">
                            <button class="flex items-center space-x-3 p-2 rounded-lg bg-gray-100 dark:bg-dark-800/50 border border-gray-300 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-dark-700/50 hover:border-gray-400 dark:hover:border-gray-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-dark-950">
                                <div class="w-8 h-8 bg-gradient-to-br from-accent-500 to-accent-700 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="text-left">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-gray-700">
                                <div class="text-sm text-gray-300">Signed in as</div>
                                <div class="text-sm font-medium text-white">{{ Auth::user()->name }}</div>
                                @if(tenancy()->initialized)
                                    <div class="text-xs text-accent-400 mt-1">{{ tenancy()->tenant->name }}</div>
                                @endif
                            </div>
                            
                            <x-dropdown-link :href="route('profile.edit')" class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>{{ __('Profile') }}</span>
                            </x-dropdown-link>

                            <div class="border-t border-gray-700"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" 
                                               onclick="event.preventDefault(); this.closest('form').submit();"
                                               class="flex items-center space-x-2 text-red-400 hover:text-red-300 hover:bg-red-900/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>{{ __('Log Out') }}</span>
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endguest
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                @auth
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-dark-800 focus:outline-none focus:bg-dark-800 focus:text-white transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endauth
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    @auth
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-dark-900/95 backdrop-blur-sm border-t border-gray-800">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ dashboard_route() }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs(dashboard_route_name()) ? 'text-accent-400 bg-dark-800' : 'text-gray-300 hover:text-white hover:bg-dark-800' }} transition-colors duration-200">
                {{ __('Dashboard') }}
            </a>
            
            @if(tenancy()->initialized)
                @can('read_projects')
                    <a href="{{ route('tenant.projects') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tenant.projects*') ? 'text-accent-400 bg-dark-800' : 'text-gray-300 hover:text-white hover:bg-dark-800' }} transition-colors duration-200">
                        {{ __('Projects') }}
                    </a>
                @endcan
                
                @can('read_tasks')
                    <a href="{{ route('tenant.tasks') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tenant.tasks*') ? 'text-accent-400 bg-dark-800' : 'text-gray-300 hover:text-white hover:bg-dark-800' }} transition-colors duration-200">
                        {{ __('Tasks') }}
                    </a>
                @endcan
                
                @can('manage_users')
                    <a href="{{ route('tenant.users.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tenant.users*') ? 'text-accent-400 bg-dark-800' : 'text-gray-300 hover:text-white hover:bg-dark-800' }} transition-colors duration-200">
                        {{ __('Users') }}
                    </a>
                @endcan
            @endif
        </div>

        <!-- User Info Section -->
        <div class="pt-4 pb-1 border-t border-gray-700 px-4">
            <div class="flex items-center space-x-3 px-3 py-2">
                <div class="w-10 h-10 bg-gradient-to-br from-accent-500 to-accent-700 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-dark-800 transition-colors duration-200">
                    {{ __('Profile') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-400 hover:text-red-300 hover:bg-red-900/20 transition-colors duration-200">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endauth
</nav>
