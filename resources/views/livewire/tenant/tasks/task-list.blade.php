<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tasks</h2>
            <p class="text-gray-600 dark:text-zinc-400">Manage your team's tasks and assignments</p>
        </div>
        <div class="flex items-center gap-2">
        <button wire:click="openCreateModal" 
            class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 focus:bg-orange-500 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Task
            </button>
        </div>
    </div>

    <!-- Global toasts are handled at the layout level; removed local flash markup -->

    <!-- Filters Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Search</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search tasks..."
                       class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Status</label>
                <select wire:model.live="statusFilter" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    @foreach($this->statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Project Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Project</label>
                <select wire:model.live="projectFilter" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Projects</option>
                    @foreach($this->projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Priority Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Priority</label>
                <select wire:model.live="priorityFilter" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Priorities</option>
                    @foreach($this->priorityOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Assigned To Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Assigned To</label>
                <select wire:model.live="assignedToFilter" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Users</option>
                    @foreach($this->users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Filter Actions -->
    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
            <div class="flex items-center gap-4">
                <label class="flex items-center">
              <input type="checkbox" 
                  wire:model.live="showCompleted"
                  class="rounded border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-blue-600 focus:ring-blue-500 focus:ring-offset-white dark:focus:ring-offset-zinc-900">
              <span class="ml-2 text-sm text-gray-700 dark:text-zinc-300">Show completed tasks</span>
                </label>
            </div>
            
            <button wire:click="clearFilters" 
              class="text-sm text-gray-600 hover:text-gray-800 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors">
                Clear all filters
            </button>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-zinc-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($this->tasks as $task)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-start">
                                    <button wire:click="toggleTaskStatus({{ $task->id }})" 
                                            class="flex-shrink-0 mr-3 mt-1">
                                        @if($task->status === 'completed')
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-400 hover:text-gray-600 dark:text-zinc-400 dark:hover:text-zinc-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                            </svg>
                                        @endif
                                    </button>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-gray-900 dark:text-white font-medium {{ $task->status === 'completed' ? 'line-through text-gray-500 dark:text-zinc-400' : '' }}">
                                            {{ $task->title }}
                                        </p>
                                        @if($task->description)
                                            <p class="text-gray-600 dark:text-zinc-400 text-sm mt-1 {{ $task->status === 'completed' ? 'line-through' : '' }}">
                                                {{ Str::limit($task->description, 60) }}
                                            </p>
                                        @endif
                                        @if($task->tags && count($task->tags) > 0)
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @foreach(array_slice($task->tags, 0, 3) as $tag)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800 dark:bg-zinc-700 dark:text-zinc-300">
                                                        {{ $tag }}
                                                    </span>
                                                @endforeach
                                                @if(count($task->tags) > 3)
                                                    <span class="text-xs text-gray-500 dark:text-zinc-500">+{{ count($task->tags) - 3 }} more</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @switch($task->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @break
                                        @case('in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @break
                                        @case('review') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 @break
                                        @case('completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                        @case('cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-zinc-700 dark:text-zinc-300
                                    @endswitch">
                                    {{ $this->statusOptions[$task->status] ?? $task->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @switch($task->priority)
                                        @case('urgent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                        @case('high') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 @break
                                        @case('medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @break
                                        @case('low') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-zinc-700 dark:text-zinc-300
                                    @endswitch">
                                    {{ $this->priorityOptions[$task->priority] ?? $task->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->project)
                                    <span class="text-gray-900 dark:text-white">{{ $task->project->name }}</span>
                                @else
                                    <span class="text-gray-500 dark:text-zinc-400">No project</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($task->assignedTo)
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 bg-orange-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">
                                                {{ substr($task->assignedTo->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-gray-900 dark:text-white text-sm">{{ $task->assignedTo->name }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-500 dark:text-zinc-400">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($task->due_date)
                                    <span class="text-gray-900 dark:text-white text-sm">
                                        {{ $task->due_date->format('M j, Y') }}
                                    </span>
                                    @if($task->due_date->isPast() && $task->status !== 'completed')
                                        <span class="ml-2 text-red-400 text-xs">Overdue</span>
                                    @endif
                                @else
                                    <span class="text-gray-500 dark:text-zinc-400">No due date</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="openEditModal({{ $task->id }})" 
                                            class="text-gray-500 hover:text-blue-600 dark:text-zinc-400 dark:hover:text-orange-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="deleteTask({{ $task->id }})" 
                                            wire:confirm="Are you sure you want to delete this task?"
                                            class="text-gray-500 hover:text-red-600 dark:text-zinc-400 dark:hover:text-red-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-zinc-400">
                                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg">No tasks found</p>
                                    <p class="text-sm">Create your first task to get started.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($this->tasks->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 dark:border-zinc-700">
                {{ $this->tasks->links() }}
            </div>
        @endif
    </div>

    <!-- Create Task Modal -->
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
                <div class="fixed inset-0 bg-black/75 transition-opacity" 
                     wire:click="closeCreateModal"
                     x-on:click="show = false"></div>
                
                <div class="inline-block align-bottom bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-700 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="createTask">
                        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Create New Task</h3>
                            
                            <!-- Task Title -->
                            <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Title *</label>
                                <input type="text" 
                                       wire:model="taskForm.title"
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter task title">
                                @error('taskForm.title') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Description</label>
                                <textarea wire:model="taskForm.description"
                                          rows="3"
                                          class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter task description"></textarea>
                                @error('taskForm.description') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status and Priority -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Status</label>
                                    <select wire:model="taskForm.status" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach($this->statusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Priority</label>
                                    <select wire:model="taskForm.priority" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach($this->priorityOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Project and Category -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Project</label>
                                    <select wire:model="taskForm.project_id" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">No project</option>
                                        @foreach($this->projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Category</label>
                                    <select wire:model="taskForm.category_id" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">No category</option>
                                        @foreach($this->categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Assigned To and Due Date -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Assigned To</label>
                                    <select wire:model="taskForm.assigned_to" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Unassigned</option>
                                        @foreach($this->users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Due Date</label>
                                    <input type="date" 
                                           wire:model="taskForm.due_date"
                       class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Estimated Hours -->
                            <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Estimated Hours</label>
                                <input type="number" 
                                       wire:model="taskForm.estimated_hours"
                                       step="0.5"
                                       min="0"
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.0">
                                @error('taskForm.estimated_hours') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
               <div class="bg-gray-50 dark:bg-zinc-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    wire:target="createTask"
                     class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="createTask">Create Task</span>
                                <span wire:loading wire:target="createTask" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating...
                                </span>
                            </button>
                            <button type="button" 
                                    wire:click="closeCreateModal"
                                    wire:loading.attr="disabled"
                     class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-zinc-600 shadow-sm px-4 py-2 bg-white dark:bg-zinc-800 text-base font-medium text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Task Modal -->
    @if($showEditModal && $editingTask)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/75 transition-opacity" wire:click="closeEditModal"></div>
                
                <div class="inline-block align-bottom bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-700 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="updateTask">
                        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Edit Task</h3>
                            
                            <!-- Task Title -->
                            <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Title *</label>
                                <input type="text" 
                                       wire:model="taskForm.title"
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter task title">
                                @error('taskForm.title') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Description</label>
                                <textarea wire:model="taskForm.description"
                                          rows="3"
                                          class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter task description"></textarea>
                                @error('taskForm.description') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status and Priority -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Status</label>
                                    <select wire:model="taskForm.status" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach($this->statusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Priority</label>
                                    <select wire:model="taskForm.priority" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach($this->priorityOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Project and Category -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Project</label>
                                    <select wire:model="taskForm.project_id" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">No project</option>
                                        @foreach($this->projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Category</label>
                                    <select wire:model="taskForm.category_id" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">No category</option>
                                        @foreach($this->categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Assigned To and Due Date -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Assigned To</label>
                                    <select wire:model="taskForm.assigned_to" 
                        class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Unassigned</option>
                                        @foreach($this->users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Due Date</label>
                                    <input type="date" 
                                           wire:model="taskForm.due_date"
                                           class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Estimated Hours -->
                            <div class="mb-4">
                     <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Estimated Hours</label>
                                <input type="number" 
                                       wire:model="taskForm.estimated_hours"
                                       step="0.5"
                                       min="0"
                         class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.0">
                                @error('taskForm.estimated_hours') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-zinc-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Save Changes
                            </button>
                            <button type="button" 
                                    wire:click="closeEditModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-zinc-600 shadow-sm px-4 py-2 bg-white dark:bg-zinc-800 text-base font-medium text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
