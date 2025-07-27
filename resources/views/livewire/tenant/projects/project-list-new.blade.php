<x-pines-layout title="Projects" subtitle="Manage and organize your projects">
    
    <!-- Header Actions -->
    <x-pines-card class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div class="flex items-center gap-3">
                @can('create_projects')
                    <x-pines-button wire:click="openCreateModal" 
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'>
                        New Project
                    </x-pines-button>
                @endcan
                
                <x-pines-button wire:click="$refresh" 
                                wire:loading.attr="disabled"
                                variant="secondary"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>'>
                    <span wire:loading.remove>Refresh</span>
                    <span wire:loading>Loading...</span>
                </x-pines-button>
            </div>
        </div>
    </x-pines-card>
    
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
                <option value="active" class="bg-black text-white">Active</option>
                <option value="completed" class="bg-black text-white">Completed</option>
                <option value="on_hold" class="bg-black text-white">On Hold</option>
                <option value="cancelled" class="bg-black text-white">Cancelled</option>
            </x-pines-select>
            
            <x-pines-select 
                wire:model.live="categoryFilter" 
                label="Category" 
                placeholder="All Categories">
                @foreach($this->categories as $category)
                    <option value="{{ $category->id }}" class="bg-black text-white">{{ $category->name }}</option>
                @endforeach
            </x-pines-select>
            
            <x-pines-select 
                wire:model.live="sortBy" 
                label="Sort By" 
                placeholder="Default">
                <option value="name" class="bg-black text-white">Name</option>
                <option value="created_at" class="bg-black text-white">Date Created</option>
                <option value="updated_at" class="bg-black text-white">Last Updated</option>
                <option value="status" class="bg-black text-white">Status</option>
            </x-pines-select>
        </div>
    </x-pines-card>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse($this->projects as $project)
            <x-pines-card class="group hover:scale-105 transition-transform duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white group-hover:text-red-400 transition-colors">
                            {{ $project->name }}
                        </h3>
                        @if($project->category)
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-red-500/20 text-red-300 rounded-full mt-2">
                                {{ $project->category->name }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($project->status === 'active') bg-green-500/20 text-green-300
                            @elseif($project->status === 'completed') bg-blue-500/20 text-blue-300
                            @elseif($project->status === 'on_hold') bg-yellow-500/20 text-yellow-300
                            @else bg-red-500/20 text-red-300 @endif">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                </div>
                
                @if($project->description)
                    <p class="text-gray-400 text-sm mb-4 line-clamp-3">{{ $project->description }}</p>
                @endif
                
                <!-- Project Stats -->
                <div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-black/20 rounded-lg">
                    <div class="text-center">
                        <div class="text-xl font-bold text-white">{{ $project->tasks_count ?? 0 }}</div>
                        <div class="text-xs text-gray-400">Tasks</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-red-400">{{ $project->completed_tasks_count ?? 0 }}</div>
                        <div class="text-xs text-gray-400">Completed</div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                @php
                    $total = $project->tasks_count ?? 0;
                    $completed = $project->completed_tasks_count ?? 0;
                    $progress = $total > 0 ? ($completed / $total) * 100 : 0;
                @endphp
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>Progress</span>
                        <span>{{ number_format($progress, 0) }}%</span>
                    </div>
                    <div class="w-full bg-black/20 rounded-full h-2">
                        <div class="bg-gradient-to-r from-red-500 to-red-400 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $progress }}%"></div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-red-500/10">
                    <div class="text-xs text-gray-400">
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
                    <svg class="mx-auto h-16 w-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h3 class="text-lg font-medium text-white mb-2">No projects found</h3>
                    <p class="text-gray-400 mb-6">Get started by creating your first project.</p>
                    @can('create_projects')
                        <x-pines-button wire:click="openCreateModal">
                            Create Project
                        </x-pines-button>
                    @endcan
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

    <!-- Create Project Modal -->
    @if($showCreateModal)
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
                <div class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm transition-opacity" 
                     @click="$wire.closeCreateModal()"></div>

                <div class="inline-block align-bottom bg-black/90 backdrop-blur-xl rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-red-500/20"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <form wire:submit="createProject">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-white" id="modal-title">
                                    Create New Project
                                </h3>
                                <button type="button" 
                                        wire:click="closeCreateModal"
                                        class="text-gray-400 hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <x-pines-input 
                                    wire:model="form.name"
                                    label="Project Name"
                                    required
                                    :error="$errors->first('form.name')"
                                    placeholder="Enter project name" />

                                <x-pines-textarea 
                                    wire:model="form.description"
                                    label="Description"
                                    :error="$errors->first('form.description')"
                                    placeholder="Enter project description" />

                                <x-pines-select 
                                    wire:model="form.category_id"
                                    label="Category"
                                    :error="$errors->first('form.category_id')"
                                    placeholder="Select a category">
                                    @foreach($this->categories as $category)
                                        <option value="{{ $category->id }}" class="bg-black text-white">{{ $category->name }}</option>
                                    @endforeach
                                </x-pines-select>

                                <x-pines-select 
                                    wire:model="form.status"
                                    label="Status"
                                    :error="$errors->first('form.status')"
                                    placeholder="Select status">
                                    <option value="active" class="bg-black text-white">Active</option>
                                    <option value="on_hold" class="bg-black text-white">On Hold</option>
                                    <option value="completed" class="bg-black text-white">Completed</option>
                                    <option value="cancelled" class="bg-black text-white">Cancelled</option>
                                </x-pines-select>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-black/20 border-t border-red-500/10 flex justify-end space-x-3">
                            <x-pines-button 
                                type="button"
                                wire:click="closeCreateModal"
                                variant="secondary">
                                Cancel
                            </x-pines-button>
                            <x-pines-button 
                                type="submit"
                                :loading="$wire.creating">
                                Create Project
                            </x-pines-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

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
                <div class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm transition-opacity" 
                     @click="$wire.closeEditModal()"></div>

                <div class="inline-block align-bottom bg-black/90 backdrop-blur-xl rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-red-500/20"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <form wire:submit="updateProject">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-white" id="modal-title">
                                    Edit Project
                                </h3>
                                <button type="button" 
                                        wire:click="closeEditModal"
                                        class="text-gray-400 hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <x-pines-input 
                                    wire:model="editForm.name"
                                    label="Project Name"
                                    required
                                    :error="$errors->first('editForm.name')"
                                    placeholder="Enter project name" />

                                <x-pines-textarea 
                                    wire:model="editForm.description"
                                    label="Description"
                                    :error="$errors->first('editForm.description')"
                                    placeholder="Enter project description" />

                                <x-pines-select 
                                    wire:model="editForm.category_id"
                                    label="Category"
                                    :error="$errors->first('editForm.category_id')"
                                    placeholder="Select a category">
                                    @foreach($this->categories as $category)
                                        <option value="{{ $category->id }}" class="bg-black text-white">{{ $category->name }}</option>
                                    @endforeach
                                </x-pines-select>

                                <x-pines-select 
                                    wire:model="editForm.status"
                                    label="Status"
                                    :error="$errors->first('editForm.status')"
                                    placeholder="Select status">
                                    <option value="active" class="bg-black text-white">Active</option>
                                    <option value="on_hold" class="bg-black text-white">On Hold</option>
                                    <option value="completed" class="bg-black text-white">Completed</option>
                                    <option value="cancelled" class="bg-black text-white">Cancelled</option>
                                </x-pines-select>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-black/20 border-t border-red-500/10 flex justify-end space-x-3">
                            <x-pines-button 
                                type="button"
                                wire:click="closeEditModal"
                                variant="secondary">
                                Cancel
                            </x-pines-button>
                            <x-pines-button 
                                type="submit"
                                :loading="$wire.updating">
                                Update Project
                            </x-pines-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Toast Notifications -->
    @if (session()->has('message'))
        <x-pines-toast 
            type="success" 
            title="Success!" 
            message="{{ session('message') }}" />
    @endif

    @if (session()->has('error'))
        <x-pines-toast 
            type="error" 
            title="Error!" 
            message="{{ session('error') }}" />
    @endif
</x-pines-layout>

<!-- Custom Styles -->
<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<!-- JavaScript for Enhanced Interactions -->
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('project-created', (event) => {
        showToast('success', 'Success!', 'Project created successfully!');
    });
    
    Livewire.on('project-updated', (event) => {
        showToast('success', 'Success!', 'Project updated successfully!');
    });
    
    Livewire.on('project-deleted', (event) => {
        showToast('success', 'Success!', 'Project deleted successfully!');
    });
});

function showToast(type, title, message) {
    const container = document.getElementById('toast-container');
    const toastHtml = `
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-init="setTimeout(() => show = false, 5000)"
             class="max-w-sm w-full pointer-events-auto">
            <div class="relative rounded-lg shadow-2xl border backdrop-blur-xl overflow-hidden bg-black/90 border-red-500/30">
                <div class="absolute inset-0 bg-gradient-to-r from-red-500/5 to-transparent"></div>
                <div class="relative p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 bg-red-500/20 rounded-full">
                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-semibold text-white">${title}</p>
                            <p class="text-sm text-gray-300 mt-1">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="show = false" class="inline-flex text-gray-400 hover:text-white focus:outline-none focus:text-white transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const toastElement = document.createElement('div');
    toastElement.innerHTML = toastHtml;
    container.appendChild(toastElement.firstElementChild);
}
</script>
