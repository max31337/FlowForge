<div>
    <!-- Trigger Button -->
    <button wire:click="openModal" 
            class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 focus:bg-orange-500 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-zinc-800 transition ease-in-out duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Task
    </button>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                
                <div class="inline-block align-bottom bg-zinc-800 rounded-lg border border-zinc-700 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="createTask">
                        <div class="bg-zinc-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-white mb-4">Create New Task</h3>
                            
                            <!-- Task Title -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-300 mb-1">Title *</label>
                                <input type="text" 
                                       wire:model="taskForm.title"
                                       class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                       placeholder="Enter task title">
                                @error('taskForm.title') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-300 mb-1">Description</label>
                                <textarea wire:model="taskForm.description"
                                          rows="3"
                                          class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                          placeholder="Enter task description"></textarea>
                                @error('taskForm.description') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status and Priority -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Status</label>
                                    <select wire:model="taskForm.status" 
                                            class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        @foreach($this->statusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('taskForm.status') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Priority</label>
                                    <select wire:model="taskForm.priority" 
                                            class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        @foreach($this->priorityOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('taskForm.priority') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Project and Category -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Project</label>
                                    <select wire:model="taskForm.project_id" 
                                            class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        <option value="">No project</option>
                                        @foreach($this->projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('taskForm.project_id') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Category</label>
                                    <select wire:model="taskForm.category_id" 
                                            class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        <option value="">No category</option>
                                        @foreach($this->categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('taskForm.category_id') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Assigned To and Estimated Hours -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Assign To</label>
                                    <select wire:model="taskForm.assigned_to" 
                                            class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        <option value="">Unassigned</option>
                                        @foreach($this->users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('taskForm.assigned_to') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Estimated Hours</label>
                                    <input type="number" 
                                           wire:model="taskForm.estimated_hours"
                                           step="0.5"
                                           min="0"
                                           class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                           placeholder="0">
                                    @error('taskForm.estimated_hours') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Due Date -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-zinc-300 mb-1">Due Date</label>
                                <input type="date" 
                                       wire:model="taskForm.due_date"
                                       class="w-full bg-zinc-900 border border-zinc-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                @error('taskForm.due_date') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-4 py-3 bg-zinc-700 text-right sm:px-6">
                            <button type="button" 
                                    wire:click="closeModal"
                                    class="inline-flex justify-center rounded-md border border-zinc-600 bg-zinc-800 px-4 py-2 text-sm font-medium text-zinc-300 hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-zinc-800 mr-3">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="inline-flex justify-center rounded-md border border-transparent bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-zinc-800">
                                Create Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
