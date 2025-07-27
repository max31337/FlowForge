<div class="space-y-8 animate-fade-in-up">
    <!-- Header Section -->
    <div class="card-glass">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white glow-text flex items-center">
                    <svg class="w-8 h-8 mr-3 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Projects
                </h1>
                <p class="text-gray-400 mt-2">Manage and organize your projects</p>
            </div>
            
            <div class="flex items-center gap-3">
                @can('create_projects')
                    <button wire:click="openCreateModal" 
                            class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Project
                    </button>
                @endcan
                
                <button wire:click="$refresh" 
                        wire:loading.attr="disabled"
                        class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span wire:loading.remove>Refresh</span>
                    <span wire:loading>Loading...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-800 border border-green-700 text-green-100 px-4 py-3 rounded relative"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             x-init="setTimeout(() => show = false, 5000)">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('message') }}
                </div>
                <button @click="show = false" class="text-green-300 hover:text-green-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-800 border border-red-700 text-red-100 px-4 py-3 rounded relative"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             x-init="setTimeout(() => show = false, 7000)">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
                <button @click="show = false" class="text-red-300 hover:text-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="bg-zinc-800 rounded-lg border border-zinc-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-zinc-300 mb-1">Search</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search projects..."
                       class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1">Status</label>
                <select wire:model.live="statusFilter" 
                        class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    @foreach($this->statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1">Category</label>
                <select wire:model.live="categoryFilter" 
                        class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    @foreach($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Priority Filter -->
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1">Priority</label>
                <select wire:model.live="priorityFilter" 
                        class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">All Priorities</option>
                    @foreach($this->priorityOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Filter Actions -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-zinc-700">
            <div class="flex items-center gap-4">
                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model.live="showInactive"
                           class="rounded border-zinc-600 bg-zinc-900 text-orange-600 focus:ring-orange-500 focus:ring-offset-zinc-800">
                    <span class="ml-2 text-sm text-zinc-300">Show inactive projects</span>
                </label>
            </div>
            
            <button wire:click="clearFilters" 
                    class="text-sm text-zinc-400 hover:text-zinc-200 transition-colors">
                Clear all filters
            </button>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($this->projects as $project)
            <div class="bg-zinc-800 border border-zinc-700 rounded-lg p-6 hover:border-zinc-600 transition-colors">
                <!-- Project Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-white truncate">{{ $project->name }}</h3>
                        @if($project->category)
                            <p class="text-sm text-zinc-400">{{ $project->category->name }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2 ml-4">
                        <!-- Status Badge -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @switch($project->status)
                                @case('planning')
                                    bg-gray-900 text-gray-200
                                    @break
                                @case('active')
                                    bg-green-900 text-green-200
                                    @break
                                @case('on_hold')
                                    bg-yellow-900 text-yellow-200
                                    @break
                                @case('completed')
                                    bg-blue-900 text-blue-200
                                    @break
                                @case('cancelled')
                                    bg-red-900 text-red-200
                                    @break
                                @default
                                    bg-zinc-700 text-zinc-300
                            @endswitch
                        ">
                            {{ $this->statusOptions[$project->status] ?? $project->status }}
                        </span>

                        <!-- Priority Badge -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @switch($project->priority)
                                @case('urgent')
                                    bg-red-900 text-red-200
                                    @break
                                @case('high')
                                    bg-orange-900 text-orange-200
                                    @break
                                @case('medium')
                                    bg-yellow-900 text-yellow-200
                                    @break
                                @case('low')
                                    bg-green-900 text-green-200
                                    @break
                                @default
                                    bg-zinc-700 text-zinc-300
                            @endswitch
                        ">
                            {{ $this->priorityOptions[$project->priority] ?? $project->priority }}
                        </span>
                    </div>
                </div>

                <!-- Project Description -->
                @if($project->description)
                    <p class="text-zinc-400 text-sm mb-4 line-clamp-3">{{ $project->description }}</p>
                @endif

                <!-- Project Stats -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white">{{ $project->tasks_count }}</p>
                        <p class="text-xs text-zinc-400">Total Tasks</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-400">{{ $project->completed_tasks_count }}</p>
                        <p class="text-xs text-zinc-400">Completed</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                @php
                    $completionRate = $project->tasks_count > 0 ? ($project->completed_tasks_count / $project->tasks_count) * 100 : 0;
                @endphp
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-zinc-400">Progress</span>
                        <span class="text-zinc-300">{{ number_format($completionRate, 1) }}%</span>
                    </div>
                    <div class="w-full bg-zinc-700 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $completionRate }}%"></div>
                    </div>
                </div>

                <!-- Project Dates -->
                <div class="text-sm text-zinc-400 space-y-1 mb-4">
                    @if($project->start_date)
                        <div class="flex justify-between">
                            <span>Start Date:</span>
                            <span>{{ $project->start_date->format('M j, Y') }}</span>
                        </div>
                    @endif
                    @if($project->due_date)
                        <div class="flex justify-between">
                            <span>Due Date:</span>
                            <span class="{{ $project->due_date->isPast() && $project->status !== 'completed' ? 'text-red-400' : '' }}">
                                {{ $project->due_date->format('M j, Y') }}
                            </span>
                        </div>
                    @endif
                    @if($project->budget)
                        <div class="flex justify-between">
                            <span>Budget:</span>
                            <span>${{ number_format($project->budget, 2) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-zinc-700">
                    <div class="flex items-center space-x-2">
                        @if($project->status === 'active')
                            <button wire:click="toggleProjectStatus({{ $project->id }})" 
                                    title="Put on hold"
                                    class="text-zinc-400 hover:text-yellow-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                        @elseif($project->status === 'on_hold')
                            <button wire:click="toggleProjectStatus({{ $project->id }})" 
                                    title="Resume project"
                                    class="text-zinc-400 hover:text-green-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </button>
                        @else
                            <button wire:click="toggleProjectStatus({{ $project->id }})" 
                                    title="Activate project"
                                    class="text-zinc-400 hover:text-green-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2">
                        <button wire:click="openEditModal({{ $project->id }})" 
                                class="text-zinc-400 hover:text-orange-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button wire:click="deleteProject({{ $project->id }})" 
                                wire:confirm="Are you sure you want to delete this project? This action cannot be undone."
                                class="text-zinc-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="text-zinc-400">
                        <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <p class="text-xl">No projects found</p>
                        <p class="text-sm">Create your first project to get started.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->projects->hasPages())
        <div class="flex justify-center">
            {{ $this->projects->links() }}
        </div>
    @endif

    <!-- Create Project Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true"
             x-data="{ show: @entangle('showCreateModal') }"
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" 
                     wire:click="closeCreateModal"
                     x-on:click="show = false"></div>
                
                <div class="inline-block align-bottom bg-zinc-800 rounded-lg border border-zinc-700 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="createProject">
                        <div class="bg-zinc-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-white mb-4">Create New Project</h3>
                            
                            <!-- Project Name -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-300 mb-1">Name *</label>
                                <input type="text" 
                                       wire:model="projectForm.name"
                                       class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                       placeholder="Enter project name">
                                @error('projectForm.name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-300 mb-1">Description</label>
                                <textarea wire:model="projectForm.description"
                                          rows="3"
                                          class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                          placeholder="Enter project description"></textarea>
                                @error('projectForm.description') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status and Priority -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Status</label>
                                    <select wire:model="projectForm.status" 
                                            class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        @foreach($this->statusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Priority</label>
                                    <select wire:model="projectForm.priority" 
                                            class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        @foreach($this->priorityOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-300 mb-1">Category</label>
                                <select wire:model="projectForm.category_id" 
                                        class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    <option value="">No category</option>
                                    @foreach($this->categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Start Date and Due Date -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Start Date</label>
                                    <input type="date" 
                                           wire:model="projectForm.start_date"
                                           class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Due Date</label>
                                    <input type="date" 
                                           wire:model="projectForm.due_date"
                                           class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    @error('projectForm.due_date') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Budget -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-300 mb-1">Budget</label>
                                <input type="number" 
                                       wire:model="projectForm.budget"
                                       step="0.01"
                                       min="0"
                                       class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                       placeholder="0.00">
                                @error('projectForm.budget') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="bg-zinc-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    wire:target="createProject"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="createProject">Create Project</span>
                                <span wire:loading wire:target="createProject" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating...
                                </span>
                            </button>
                            <button type="button" 
                                    wire:click="closeCreateModal"
                                    x-on:click="show = false"
                                    wire:loading.attr="disabled"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-zinc-600 shadow-sm px-4 py-2 bg-zinc-800 text-base font-medium text-zinc-300 hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Project Modal -->
    @if($showEditModal && $editingProject)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" wire:click="closeEditModal"></div>
                
                <div class="inline-block align-bottom bg-zinc-800 rounded-lg border border-zinc-700 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="updateProject">
                        <div class="bg-zinc-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-white mb-4">Edit Project</h3>
                            
                            <!-- Project Name -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-300 mb-1">Name *</label>
                                <input type="text" 
                                       wire:model="projectForm.name"
                                       class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                       placeholder="Enter project name">
                                @error('projectForm.name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-300 mb-1">Description</label>
                                <textarea wire:model="projectForm.description"
                                          rows="3"
                                          class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                          placeholder="Enter project description"></textarea>
                                @error('projectForm.description') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status and Priority -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Status</label>
                                    <select wire:model="projectForm.status" 
                                            class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        @foreach($this->statusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Priority</label>
                                    <select wire:model="projectForm.priority" 
                                            class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        @foreach($this->priorityOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-300 mb-1">Category</label>
                                <select wire:model="projectForm.category_id" 
                                        class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    <option value="">No category</option>
                                    @foreach($this->categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Start Date and Due Date -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Start Date</label>
                                    <input type="date" 
                                           wire:model="projectForm.start_date"
                                           class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Due Date</label>
                                    <input type="date" 
                                           wire:model="projectForm.due_date"
                                           class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    @error('projectForm.due_date') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Budget -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-300 mb-1">Budget</label>
                                <input type="number" 
                                       wire:model="projectForm.budget"
                                       step="0.01"
                                       min="0"
                                       class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                       placeholder="0.00">
                                @error('projectForm.budget') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="bg-zinc-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    wire:target="updateProject"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="updateProject">Update Project</span>
                                <span wire:loading wire:target="updateProject" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Updating...
                                </span>
                            </button>
                            <button type="button" 
                                    wire:click="closeEditModal"
                                    wire:loading.attr="disabled"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-zinc-600 shadow-sm px-4 py-2 bg-zinc-800 text-base font-medium text-zinc-300 hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
