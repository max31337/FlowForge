<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                <!-- Dashboard (uses RouteHelper to support central & tenant contexts) -->
                @php($dashName = dashboard_route_name())
                <li>
                    <a href="{{ dashboard_route() }}"
                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                              {{ request()->routeIs($dashName) 
                                    ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' 
                                    : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-blue-900/20' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs($dashName) ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        Dashboard
                    </a>
                </li>

                @if(tenancy()->initialized)
                    <!-- Tenant-only links -->
                    @can('read_projects')
                    <li>
                        <a href="{{ route('tenant.projects.index') }}"
                           class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                  {{ request()->routeIs('tenant.projects*') 
                                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' 
                                        : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-blue-900/20' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('tenant.projects*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                            </svg>
                            Projects
                        </a>
                    </li>
                    @endcan

                    @can('read_tasks')
                    <li>
                        <a href="{{ route('tenant.tasks.index') }}"
                           class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                  {{ request()->routeIs('tenant.tasks*') 
                                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' 
                                        : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-blue-900/20' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('tenant.tasks*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-6.75V6A2.25 2.25 0 0016.5 3.75h-6a2.25 2.25 0 00-2.25 2.25v7.5m3 0V9a2.25 2.25 0 012.25-2.25h1.5A2.25 2.25 0 0118 9v10.5M6 9.75v10.5a2.25 2.25 0 002.25 2.25h7.5" />
                            </svg>
                            Tasks
                        </a>
                    </li>
                    @endcan

                    @can('manage_users')
                    <li>
                        <a href="{{ route('tenant.users.index') }}"
                           class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                  {{ request()->routeIs('tenant.users*') 
                                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' 
                                        : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-blue-900/20' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('tenant.users*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            Team
                        </a>
                    </li>
                    @endcan
                @else
                    <!-- Central admin links -->
                    <li>
                        <a href="{{ route('admin.tenants.index') }}"
                           class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold
                                  {{ request()->routeIs('admin.tenants*') 
                                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' 
                                        : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-blue-900/20' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.tenants*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            Tenants
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <!-- Analytics Section -->
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400 dark:text-gray-500">Analytics</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <li>
                    <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-blue-900/20">
                        <svg class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-blue-600 dark:text-gray-500 dark:group-hover:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        Reports
                    </a>
                </li>
                <li>
                    <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-blue-900/20">
                        <svg class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-blue-600 dark:text-gray-500 dark:group-hover:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                        </svg>
                        Insights
                    </a>
                </li>
            </ul>
        </li>

        <!-- Settings -->
        <li class="-mx-6 mt-auto">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-zinc-700 flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <span class="sr-only">Your profile</span>
                <span>{{ Auth::user()->name }}</span>
            </a>
        </li>
    </ul>
</nav>
