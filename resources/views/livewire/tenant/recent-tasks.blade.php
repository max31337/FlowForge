<div class="bg-white dark:bg-zinc-900 rounded-lg shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800">
    <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Tasks</h3>
            @if($this->tasks->count() > 0)
                <button 
                    wire:click="toggleShowAll" 
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 transition-colors duration-200"
                >
                    {{ $showAll ? 'Show Less' : 'Show All' }}
                </button>
            @endif
        </div>
        
        <!-- Status Filter Tabs -->
        <div class="flex space-x-1 bg-gray-100 dark:bg-zinc-800 rounded-lg p-1">
            @foreach(['all' => 'All', 'pending' => 'Pending', 'in_progress' => 'In Progress', 'review' => 'Review', 'completed' => 'Completed'] as $status => $label)
                <button 
                    wire:click="setFilterStatus('{{ $status }}')"
                    class="flex-1 text-xs font-medium py-2 px-3 rounded-md transition-colors duration-200
                        @if($filterStatus === $status) 
                            bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm
                        @else 
                            text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white
                        @endif"
                >
                    {{ $label }}
                    @if($this->statusCounts[$status] > 0)
                        <span class="ml-1 bg-gray-200 dark:bg-gray-500 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded-full text-xs">
                            {{ $this->statusCounts[$status] }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    
    <div class="p-6">
        @if($this->tasks->count() > 0)
            <div class="space-y-3">
                @foreach($this->tasks as $task)
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex-1">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $task->title }}</h4>
                                    
                                    @if($task->project)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="fas fa-project-diagram mr-1"></i>{{ $task->project->name }}
                                        </p>
                                    @endif
                                    
                                    @if($task->assignedTo)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="fas fa-user mr-1"></i>Assigned to {{ $task->assignedTo->name }}
                                        </p>
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-2 ml-4">
                                    <!-- Priority Badge -->
                                    @if($task->priority)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($task->priority === 'urgent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($task->priority === 'high') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                            @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @endif">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    @endif
                                    
                                    <!-- Status Dropdown -->
                                    @can('update_tasks')
                                        <select 
                                            wire:change="updateTaskStatus({{ $task->id }}, $event.target.value)"
                                            class="text-xs border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="pending" @if($task->status === 'pending') selected @endif>Pending</option>
                                            <option value="in_progress" @if($task->status === 'in_progress') selected @endif>In Progress</option>
                                            <option value="review" @if($task->status === 'review') selected @endif>Review</option>
                                            <option value="completed" @if($task->status === 'completed') selected @endif>Completed</option>
                                        </select>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($task->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($task->status === 'review') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    @endcan
                                </div>
                            </div>
                            
                            <!-- Due Date -->
                            @if($task->due_date)
                                <div class="flex items-center mt-2 text-xs">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                    <span class="text-gray-600 dark:text-gray-400
                                        @if($task->due_date->isPast() && $task->status !== 'completed') text-red-600 dark:text-red-400 @endif">
                                        Due {{ $task->due_date->diffForHumans() }}
                                        @if($task->due_date->isPast() && $task->status !== 'completed')
                                            <i class="fas fa-exclamation-triangle ml-1"></i>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="text-right ml-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $task->updated_at->diffForHumans() }}
                            </div>
                            @can('read_tasks')
                                <button class="mt-2 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                    View Details
                                </button>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
            
            @can('manage_tasks')
                <div class="mt-6 text-center">
                    <button class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                        <i class="fas fa-plus mr-2"></i>Create New Task
                    </button>
                </div>
            @endcan
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-tasks text-4xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    @if($filterStatus === 'all')
                        No tasks yet
                    @else
                        No {{ str_replace('_', ' ', $filterStatus) }} tasks
                    @endif
                </h4>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    @if($filterStatus === 'all')
                        Start managing your work by creating your first task
                    @else
                        Try switching to a different status filter
                    @endif
                </p>
                
                @if($filterStatus === 'all')
                    @can('manage_tasks')
                        <button class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                            <i class="fas fa-plus mr-2"></i>Create Task
                        </button>
                    @endcan
                @else
                    <button 
                        wire:click="setFilterStatus('all')"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out"
                    >
                        View All Tasks
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
