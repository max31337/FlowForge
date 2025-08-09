<div class="bg-white dark:bg-zinc-900 rounded-lg shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:ring-zinc-800">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Task Status Distribution</h3>
    </div>
    <div class="p-6">
        @php($total = $this->counts['total'])
        @if($total > 0)
            @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'review' => 'Review', 'completed' => 'Completed'] as $key => $label)
                @php($count = $this->counts[$key])
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                        <div 
                            class="h-2 rounded-full transition-all duration-700
                                @if($key === 'completed') bg-green-500
                                @elseif($key === 'in_progress') bg-blue-500
                                @elseif($key === 'review') bg-purple-500
                                @else bg-yellow-500 @endif" 
                            style="width: {{ $total > 0 ? round(($count / $total) * 100) : 0 }}%">
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-gray-500 dark:text-gray-400">No task data available.</p>
        @endif
    </div>
</div>
