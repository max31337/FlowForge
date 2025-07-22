<?php

namespace App\Livewire\Tenant;

use App\Models\Task;
use Livewire\Component;
use Livewire\Attributes\Computed;

class RecentTasks extends Component
{
    public int $limit = 5;
    public bool $showAll = false;
    public string $filterStatus = 'all';

    protected $queryString = ['filterStatus'];

    #[Computed]
    public function tasks()
    {
        if (!tenancy()->initialized) {
            return collect();
        }

        $query = Task::where('tenant_id', tenant('id'))
            ->with(['project:id,name', 'assignedTo:id,name'])
            ->orderBy('updated_at', 'desc');

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if (!$this->showAll) {
            $query->limit($this->limit);
        }

        return $query->get();
    }

    #[Computed]
    public function statusCounts()
    {
        if (!tenancy()->initialized) {
            return [
                'all' => 0,
                'pending' => 0,
                'in_progress' => 0,
                'review' => 0,
                'completed' => 0,
            ];
        }

        $counts = Task::where('tenant_id', tenant('id'))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'all' => array_sum($counts),
            'pending' => $counts['pending'] ?? 0,
            'in_progress' => $counts['in_progress'] ?? 0,
            'review' => $counts['review'] ?? 0,
            'completed' => $counts['completed'] ?? 0,
        ];
    }

    public function setFilterStatus($status)
    {
        $this->filterStatus = $status;
        unset($this->tasks);
    }

    public function toggleShowAll()
    {
        $this->showAll = !$this->showAll;
        unset($this->tasks);
    }

    public function updateTaskStatus($taskId, $status)
    {
        $task = Task::where('tenant_id', tenant('id'))->findOrFail($taskId);
        
        // Check permissions
        if (!auth()->user()->hasPermission('update_tasks')) {
            $this->dispatch('error', 'You do not have permission to update tasks.');
            return;
        }

        $task->update(['status' => $status]);
        
        // Clear computed properties cache
        unset($this->tasks, $this->statusCounts);
        
        $this->dispatch('task-updated', $taskId, $status);
        $this->dispatch('success', 'Task status updated successfully!');
    }

    public function render()
    {
        return view('livewire.tenant.recent-tasks');
    }
}
