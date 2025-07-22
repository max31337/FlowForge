<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Projects</h3>
            @if($this->projects->count() > 0)
                <button 
                    wire:click="toggleShowAll" 
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 transition-colors duration-200"
                >
                    {{ $showAll ? 'Show Less' : 'Show All' }}
                </button>
            @endif
        </div>
    </div>
    
    <div class="p-6">
        @if($this->projects->count() > 0)
            <div class="space-y-4">
                @foreach($this->projects as $project)
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($project->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($project->status === 'active') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($project->status === 'on_hold') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </div>
                            
                            @if($project->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ Str::limit($project->description, 80) }}
                                </p>
                            @endif
                            
                            <!-- Progress bar -->
                            @if($project->total_tasks > 0)
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span class="text-gray-600 dark:text-gray-400">
                                            {{ $project->completed_tasks }}/{{ $project->total_tasks }} tasks
                                        </span>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $project->progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                        <div 
                                            class="h-1.5 rounded-full transition-all duration-500 ease-out
                                                @if($project->progress >= 80) bg-green-500
                                                @elseif($project->progress >= 50) bg-blue-500
                                                @elseif($project->progress >= 25) bg-yellow-500
                                                @else bg-red-500
                                                @endif" 
                                            style="width: {{ $project->progress }}%"
                                        ></div>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">No tasks yet</p>
                            @endif
                        </div>
                        
                        <div class="ml-4 text-right">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $project->updated_at->diffForHumans() }}
                            </div>
                            @can('manage_projects')
                                <button class="mt-2 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                    View Details
                                </button>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
            
            @can('manage_projects')
                <div class="mt-6 text-center">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                        <i class="fas fa-plus mr-2"></i>Create New Project
                    </button>
                </div>
            @endcan
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-project-diagram text-4xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No projects yet</h4>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first project</p>
                
                @can('manage_projects')
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                        <i class="fas fa-plus mr-2"></i>Create Project
                    </button>
                @endcan
            </div>
        @endif
    </div>
</div>
