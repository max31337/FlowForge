<?php

namespace App\Livewire\Tenant\Tasks;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Livewire\TenantAwareComponent;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class TaskList extends TenantAwareComponent
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $projectFilter = '';
    public $priorityFilter = '';
    public $assignedToFilter = '';
    public $showCompleted = true;
    public $perPage = 10;

    // Task creation/editing properties
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingTask = null;
    
    public $taskForm = [
        'title' => '',
        'description' => '',
        'status' => 'pending',
        'priority' => 'medium',
        'estimated_hours' => null,
        'due_date' => null,
        'project_id' => null,
        'category_id' => null,
        'assigned_to' => null,
        'tags' => [],
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'projectFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'assignedToFilter' => ['except' => ''],
        'showCompleted' => ['except' => true],
    ];

    protected function rules() 
    {
        $tenantId = $this->getTenantId();
        
        if (!$tenantId) {
            return [];
        }
        
        return [
            'taskForm.title' => 'required|string|max:255',
            'taskForm.description' => 'nullable|string',
            'taskForm.status' => 'required|in:pending,in_progress,review,completed,cancelled',
            'taskForm.priority' => 'required|in:low,medium,high,urgent',
            'taskForm.estimated_hours' => 'nullable|numeric|min:0|max:1000',
            'taskForm.due_date' => 'nullable|date|after:today',
            'taskForm.project_id' => 'nullable|exists:projects,id,tenant_id,' . $tenantId,
            'taskForm.category_id' => 'nullable|exists:categories,id,tenant_id,' . $tenantId,
            'taskForm.assigned_to' => 'nullable|exists:users,id,tenant_id,' . $tenantId,
        ];
    }

    public function mount()
    {
        // Reset pagination when component mounts
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingProjectFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatingAssignedToFilter()
    {
        $this->resetPage();
    }

    public function toggleCompleted()
    {
        $this->showCompleted = !$this->showCompleted;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'projectFilter', 'priorityFilter', 'assignedToFilter']);
        $this->showCompleted = true;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetTaskForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetTaskForm();
        $this->resetValidation();
    }

    public function openEditModal($taskId)
    {
        // Ensure tenant context and task belongs to current tenant
        if (!tenancy()->initialized) {
            return;
        }

        $this->editingTask = Task::where('tenant_id', tenant('id'))
            ->findOrFail($taskId);
            
        $this->taskForm = [
            'title' => $this->editingTask->title ?? '',
            'description' => $this->editingTask->description ?? '',
            'status' => $this->editingTask->status ?? 'pending',
            'priority' => $this->editingTask->priority ?? 'medium',
            'estimated_hours' => $this->editingTask->estimated_hours ?? null,
            'due_date' => $this->editingTask->due_date ?? null,
            'project_id' => $this->editingTask->project_id ?? null,
            'category_id' => $this->editingTask->category_id ?? null,
            'assigned_to' => $this->editingTask->assigned_to ?? null,
            'tags' => $this->editingTask->tags ?? [],
        ];
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingTask = null;
        $this->resetTaskForm();
        $this->resetValidation();
    }

    public function createTask()
    {
        try {
            $this->validate();

            $data = $this->taskForm;
            $data['created_by'] = Auth::id();
            
            // Get tenant_id directly - use fallback if needed
            if (tenancy()->initialized && tenant('id')) {
                $data['tenant_id'] = tenant('id');
            } else {
                // Fallback: try to get tenant from current domain
                $host = request()->getHost();
                $tenant = \App\Models\Tenant::whereHas('domains', function($query) use ($host) {
                    $query->where('domain', $host);
                })->first();
                
                if ($tenant) {
                    $data['tenant_id'] = $tenant->id;
                } else {
                    throw new \Exception('Unable to determine tenant context');
                }
            }
            
            // Remove empty values (but keep tenant_id and created_by)
            $data = array_filter($data, function($value, $key) {
                return in_array($key, ['tenant_id', 'created_by']) || ($value !== null && $value !== '');
            }, ARRAY_FILTER_USE_BOTH);

            Task::create($data);

            $this->closeCreateModal();
            $this->dispatch('task-created');
            session()->flash('message', 'Task created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to create task: ' . $e->getMessage());
            $this->closeCreateModal();
        }
    }

    public function updateTask()
    {
        // Ensure tenant context
        if (!tenancy()->initialized || !$this->editingTask) {
            session()->flash('error', 'Tenant context not available or task not found.');
            return;
        }

        $this->validate();

        $data = $this->taskForm;
        
        // Remove empty values
        $data = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        $this->editingTask->update($data);

        $this->closeEditModal();
        $this->dispatch('task-updated');
        session()->flash('message', 'Task updated successfully!');
    }

    public function deleteTask($taskId)
    {
        // Ensure tenant context and task belongs to current tenant
        if (!tenancy()->initialized) {
            return;
        }

        $task = Task::where('tenant_id', tenant('id'))->findOrFail($taskId);
        $task->delete();

        $this->dispatch('task-deleted');
        session()->flash('message', 'Task deleted successfully!');
    }

    public function toggleTaskStatus($taskId)
    {
        // Ensure tenant context and task belongs to current tenant
        if (!tenancy()->initialized) {
            return;
        }

        $task = Task::where('tenant_id', tenant('id'))->findOrFail($taskId);
        
        if ($task->status === 'completed') {
            $task->update([
                'status' => 'pending',
                'completed_at' => null,
            ]);
        } else {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        $this->dispatch('task-status-changed');
    }

    private function resetTaskForm()
    {
        $this->taskForm = [
            'title' => '',
            'description' => '',
            'status' => 'pending',
            'priority' => 'medium',
            'estimated_hours' => null,
            'due_date' => null,
            'project_id' => null,
            'category_id' => null,
            'assigned_to' => null,
            'tags' => [],
        ];
    }

    #[Computed]
    public function tasks()
    {
        $tenantId = $this->getTenantId();
        
        if (!$tenantId) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                [], 0, $this->perPage, 1, ['path' => request()->url()]
            );
        }

        $query = Task::with(['project', 'category', 'assignedTo', 'createdBy'])
            ->where('tenant_id', $tenantId)
            ->when($this->search, function($q) {
                $q->where(function($subQ) {
                    $subQ->where('title', 'like', '%' . $this->search . '%')
                         ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->projectFilter, function($q) {
                $q->where('project_id', $this->projectFilter);
            })
            ->when($this->priorityFilter, function($q) {
                $q->where('priority', $this->priorityFilter);
            })
            ->when($this->assignedToFilter, function($q) {
                $q->where('assigned_to', $this->assignedToFilter);
            })
            ->when(!$this->showCompleted, function($q) {
                $q->where('status', '!=', 'completed');
            })
            ->orderBy('created_at', 'desc');

        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function projects()
    {
        $tenantId = $this->getTenantId();
        
        if (!$tenantId) {
            return collect();
        }

        return Project::where('tenant_id', $tenantId)
            ->active()
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function categories()
    {
        $tenantId = $this->getTenantId();
        
        if (!$tenantId) {
            return collect();
        }

        return Category::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function users()
    {
        $tenantId = $this->getTenantId();
        
        if (!$tenantId) {
            return collect();
        }

        return User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function statusOptions()
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'review' => 'In Review',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    #[Computed]
    public function priorityOptions()
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    public function render()
    {
        return view('livewire.tenant.tasks.task-list');
    }
}
