<div x-data="{ columns: @entangle('columns') }">
    <div class="kanban-board grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 p-6">
        <div class="kanban-columns space-x-6 flex overflow-x-auto" x-ref="columns">
            @foreach($columns as $column)
                <div class="kanban-column bg-white rounded-lg shadow-lg p-4 flex flex-col space-y-4" data-id="{{ $column->id }}" id="column-{{ $column->id }}">
                    <div class="kanban-column-header flex items-center justify-between space-x-2">
                        <input type="text" value="{{ $column->title }}" class="column-title text-lg font-semibold text-gray-800 border-none outline-none focus:ring-2 focus:ring-indigo-600 p-2 rounded-md w-full" x-model="columns.find(col => col.id == {{ $column->id }}).title" />
                    </div>
                    <div class="kanban-column-body space-y-4" id="column-body-{{ $column->id }}">
                        @foreach($tasks->get($column->title, []) as $task)
                            <div class="kanban-task bg-white p-4 rounded-lg shadow-md cursor-move border border-gray-200 hover:border-gray-300 transition-all" data-id="{{ $task->id }}">
                                <div class="kanban-task-content flex flex-col">
                                    <h4 class="font-semibold text-gray-900">{{ $task->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $task->description }}</p>
                                    <div class="mt-2">
                                        @foreach($task->labels as $label)
                                            <span class="inline-block bg-{{ $label->color }}-500 text-white text-xs rounded-full py-1 px-2 mr-2">{{ $label->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="kanban-controls mt-6">
        <button @click="addColumn()" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md shadow-md transition-all">Add Column</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kanbanColumns = document.getElementById('kanban-columns');
        new Swapy({
            targets: '.kanban-column-body',
            handle: '.kanban-task',
            onDrop: (src, dest) => {
                let srcColumnId = src.closest('.kanban-column').dataset.id;
                let destColumnId = dest.closest('.kanban-column').dataset.id;

                // You can make an AJAX request to update the task's column
                Livewire.emit('updateTaskStatus', src.dataset.id, destColumnId);
            }
        });

        function addColumn() {
            // You can make an AJAX request to add a new column
            Livewire.emit('addColumn', 'New Column');
        }
    });
</script>

<style>
    .kanban-column-header input {
        background-color: #f9fafb;
    }
    .kanban-column-body {
        min-height: 200px;
    }
    .kanban-task {
        background-color: #f3f4f6;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .kanban-task:hover {
        background-color: #e2e8f0;
    }
</style>