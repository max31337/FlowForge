<div>
    <x-pines-layout title="Projects" subtitle="Manage your organization's projects" :embedded="true">
        
        <!-- Header with actions on the right -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Projects</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Manage your organization's projects</p>
            </div>
            <div class="flex items-center gap-3">
                <x-pines-button wire:click="$refresh" 
                                wire:loading.attr="disabled"
                                variant="secondary"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>'>
                    <span wire:loading.remove>Refresh</span>
                    <span wire:loading>Loading...</span>
                </x-pines-button>
                @can('create_projects')
                    @livewire('tenant.projects.create-project-form')
                @endcan
            </div>
        </div>
    
    <!-- Filters Section -->
    <x-pines-card class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-pines-input 
                wire:model.live="search" 
                placeholder="Search projects..." 
                label="Search" />
            
            <x-pines-select 
                wire:model.live="statusFilter" 
                label="Status" 
                placeholder="All Statuses">
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="on_hold">On Hold</option>
                <option value="cancelled">Cancelled</option>
            </x-pines-select>
            
            <x-pines-select 
                wire:model.live="categoryFilter" 
                label="Category" 
                placeholder="All Categories">
                @foreach($this->categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </x-pines-select>
            
            <x-pines-select 
                wire:model.live="sortBy" 
                label="Sort By" 
                placeholder="Default">
                <option value="name">Name</option>
                <option value="created_at">Date Created</option>
                <option value="updated_at">Last Updated</option>
                <option value="status">Status</option>
            </x-pines-select>
        </div>
    </x-pines-card>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse($this->projects as $project)
            <x-pines-card class="group hover:scale-105 transition-transform duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                            {{ $project->name }}
                        </h3>
                        @if($project->category)
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full mt-2 bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-300">
                                {{ $project->category->name }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        @php
                            $statusClass = match($project->status) {
                                'active' => 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-300',
                                'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300',
                                'on_hold' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-300',
                                default => 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-300',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                </div>
                
                @if($project->description)
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">{{ $project->description }}</p>
                @endif
                
                <!-- Project Stats -->
                <div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-gray-50 dark:bg-black/20 rounded-lg">
                    <div class="text-center">
                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $project->tasks_count ?? 0 }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Tasks</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-red-600 dark:text-red-400">{{ $project->completed_tasks_count ?? 0 }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Completed</div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                @php
                    $total = $project->tasks_count ?? 0;
                    $completed = $project->completed_tasks_count ?? 0;
                    $progress = $total > 0 ? ($completed / $total) * 100 : 0;
                @endphp
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                        <span>Progress</span>
                        <span>{{ number_format($progress, 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-black/20 rounded-full h-2">
                        <div class="bg-gradient-to-r from-red-500 to-red-400 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $progress }}%"></div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-red-500/10">
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        {{ $project->created_at->diffForHumans() }}
                    </div>
                    
                    <div class="flex space-x-2">
                        @can('update_projects')
                            <x-pines-button 
                                wire:click="openEditModal({{ $project->id }})"
                                variant="ghost" 
                                size="sm"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'>
                                Edit
                            </x-pines-button>
                        @endcan
                        
                        @can('delete_projects')
                            <x-pines-button 
                                wire:click="confirmDelete({{ $project->id }})"
                                wire:confirm="Are you sure you want to delete this project?"
                                variant="danger" 
                                size="sm"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>'>
                                Delete
                            </x-pines-button>
                        @endcan
                    </div>
                </div>
            </x-pines-card>
        @empty
            <div class="col-span-full">
                <x-pines-card class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No projects found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Get started by creating your first project.</p>
                </x-pines-card>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->projects->hasPages())
        <div class="flex justify-center">
            {{ $this->projects->links() }}
        </div>
    @endif

    <!-- Create Project Modal moved to its own component -->

    <!-- Edit Project Modal -->
    @if($showEditModal && $editingProject)
    <div class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity" 
                     @click="$wire.closeEditModal()"></div>

                <div class="inline-block align-bottom bg-white dark:bg-zinc-900 backdrop-blur-xl rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-red-500/20"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <form wire:submit="updateProject">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">
                                    Edit Project
                                </h3>
                                <button type="button" 
                                        wire:click="closeEditModal"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <x-pines-input 
                                    wire:model="projectForm.name"
                                    label="Project Name"
                                    required
                                    :error="$errors->first('projectForm.name')"
                                    placeholder="Enter project name" />

                                <x-pines-textarea 
                                    wire:model="projectForm.description"
                                    label="Description"
                                    :error="$errors->first('projectForm.description')"
                                    placeholder="Enter project description" />

                                <x-pines-select 
                                    wire:model="projectForm.category_id"
                                    label="Category"
                                    :error="$errors->first('projectForm.category_id')"
                                    placeholder="Select a category">
                                    @foreach($this->categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </x-pines-select>

                                <x-pines-select 
                                    wire:model="projectForm.status"
                                    label="Status"
                                    :error="$errors->first('projectForm.status')"
                                    placeholder="Select status">
                                    <option value="active">Active</option>
                                    <option value="on_hold">On Hold</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </x-pines-select>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 dark:bg-black/20 border-t border-gray-200 dark:border-red-500/10 flex justify-end space-x-3">
                            <x-pines-button 
                                type="button"
                                wire:click="closeEditModal"
                                variant="secondary">
                                Cancel
                            </x-pines-button>
                            <x-pines-button 
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="updateProject">
                                <span wire:loading.remove wire:target="updateProject">Update Project</span>
                                <span wire:loading wire:target="updateProject">Updating...</span>
                            </x-pines-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    </x-pines-layout>
</div>

<!-- styles for this view were removed from push to avoid Blade stack parsing issues -->
