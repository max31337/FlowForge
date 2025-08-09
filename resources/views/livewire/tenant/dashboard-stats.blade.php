<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Dashboard Overview</h2>
        <div class="flex space-x-2">
            @if(config('app.debug'))
                <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                    {{ $this->stats['debug_info'] ?? 'No debug info' }}
                </span>
            @endif
            <button 
                wire:click="refresh" 
                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200"
                wire:loading.attr="disabled"
            >
                <i class="fas fa-sync-alt" wire:loading.class="animate-spin"></i>
                <span wire:loading.remove>Refresh</span>
                <span wire:loading>Refreshing...</span>
            </button>
        </div>
    </div>

    @if($loading)
        <!-- Loading skeleton -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-8">
            @for($i = 0; $i < 6; $i++)
                <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800 p-6 animate-pulse">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-gray-200 dark:bg-zinc-700 w-12 h-12"></div>
                        <div class="ml-4 flex-1">
                            <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded w-3/4 mb-2"></div>
                            <div class="h-6 bg-gray-200 dark:bg-zinc-700 rounded w-1/2"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    @else
        <!-- Tenant Info Banner -->
        @if(tenancy()->initialized)
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-sm border border-blue-200 dark:border-blue-800 p-4 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                            <i class="fas fa-building text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">{{ tenancy()->tenant->name }}</h3>
                            <p class="text-blue-100 text-sm">
                                <i class="fas fa-users mr-1"></i>{{ $this->stats['total_users'] }} team members
                                <span class="mx-2">•</span>
                                <i class="fas fa-chart-line mr-1"></i>{{ $this->stats['completion_rate'] }}% completion rate
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-8" wire:transition>
            <!-- Total Projects -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800 p-6 transition-transform duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-500 bg-opacity-20">
                        <i class="fas fa-project-diagram text-blue-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Projects</h3>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $this->stats['total_projects'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Tasks -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800 p-6 transition-transform duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-500 bg-opacity-20">
                        <i class="fas fa-tasks text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Tasks</h3>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $this->stats['total_tasks'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Tasks -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800 p-6 transition-transform duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20">
                        <i class="fas fa-clock text-yellow-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</h3>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $this->stats['pending_tasks'] }}</p>
                    </div>
                </div>
            </div>

            <!-- In Progress Tasks -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800 p-6 transition-transform duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-500 bg-opacity-20">
                        <i class="fas fa-spinner text-indigo-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">In Progress</h3>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $this->stats['in_progress_tasks'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Completed Tasks -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800 p-6 transition-transform duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-500 bg-opacity-20">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Completed</h3>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $this->stats['completed_tasks'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800 p-6 transition-transform duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-500 bg-opacity-20">
                        <i class="fas fa-users text-purple-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Team</h3>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $this->stats['total_users'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Rate Progress Bar -->
        @if($this->stats['total_tasks'] > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Completion Rate</h3>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->stats['completion_rate'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div 
                        class="bg-gradient-to-r from-green-400 to-green-600 h-2.5 rounded-full transition-all duration-1000 ease-out" 
                        style="width: {{ $this->stats['completion_rate'] }}%"
                    ></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    {{ $this->stats['completed_tasks'] }} of {{ $this->stats['total_tasks'] }} tasks completed
                </p>
            </div>
        @endif
    @endif
</div>
