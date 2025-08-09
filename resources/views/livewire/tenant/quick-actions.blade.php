<div class="bg-white dark:bg-zinc-900 rounded-lg shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800 p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
        @if(tenancy()->initialized)
            <span class="text-xs text-gray-500 dark:text-gray-400">
                <i class="fas fa-building mr-1"></i>{{ tenancy()->tenant->name }}
            </span>
        @endif
    </div>

    @if(count($actions) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($actions as $action)
                <div class="group relative">
                    <a href="{{ $action['url'] }}" 
                       class="block p-4 border border-gray-200 dark:border-zinc-700 rounded-lg hover:border-{{ $action['color'] }}-300 dark:hover:border-{{ $action['color'] }}-600 transition-all duration-200 hover:shadow-md">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-{{ $action['color'] }}-100 dark:bg-{{ $action['color'] }}-900 rounded-lg flex items-center justify-center group-hover:bg-{{ $action['color'] }}-200 dark:group-hover:bg-{{ $action['color'] }}-800 transition-colors duration-200">
                                    <i class="{{ $action['icon'] }} text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-{{ $action['color'] }}-600 dark:group-hover:text-{{ $action['color'] }}-400 transition-colors duration-200">
                                    {{ $action['title'] }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $action['description'] }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-{{ $action['color'] }}-500 transition-colors duration-200 opacity-0 group-hover:opacity-100"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-lock text-gray-400 text-3xl mb-3"></i>
            <p class="text-gray-600 dark:text-gray-400">No actions available with your current permissions.</p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                Contact your administrator to request additional access.
            </p>
        </div>
    @endif
</div>
