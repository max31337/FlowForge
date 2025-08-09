<div>
    <!-- Trigger Button -->
    <x-pines-button wire:click="openModal"
                    icon='&lt;path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/&gt;'>
        New Project
    </x-pines-button>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true"
             x-data="{ show: @entangle('showModal') }"
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity" 
                     x-on:click="show = false; $wire.closeModal()"></div>

                <div class="inline-block align-bottom bg-white dark:bg-zinc-900 backdrop-blur-xl rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-red-500/20"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <form wire:submit.prevent="createProject">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">
                                    Create New Project
                                </h3>
                                <button type="button" 
                                        wire:click="closeModal"
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
                                wire:click="closeModal"
                                variant="secondary">
                                Cancel
                            </x-pines-button>
                            <x-pines-button 
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="createProject">
                                <span wire:loading.remove wire:target="createProject">Create Project</span>
                                <span wire:loading wire:target="createProject">Creating...</span>
                            </x-pines-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
