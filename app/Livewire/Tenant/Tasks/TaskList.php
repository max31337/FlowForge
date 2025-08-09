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
        
        // Clear any error messages
        session()->forget(['error']);
        
        // Dispatch event to close modal via JavaScript
        $this->dispatch('close-modal');
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
        
        // Clear any error messages
        session()->forget(['error']);
        
        // Force refresh the component state
        $this->dispatch('$refresh');
    }

    public function createTask()
    {
        try {
            // Validate the form
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

            // Dispatch global toast
            $this->dispatch('toast', type: 'success', title: 'Task Created', message: 'Task created successfully!', duration: 4000);
            
            // Close modal and reset form
            $this->closeCreateModal();
            
            // Dispatch events for other components to update
            $this->dispatch('task-created');
            $this->dispatch('refresh-task-list');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to show in UI
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', title: 'Create Failed', message: 'Unable to create task: ' . $e->getMessage(), duration: 6000);
            // Don't close modal on error, let user try again
        }
    }

    public function updateTask()
    {
        try {
            if (!tenancy()->initialized || !$this->editingTask) {
                throw new \Exception('Tenant context not available or task not found.');
            }

            $this->validate();

            $data = $this->taskForm;
            
            // Remove empty values
            $data = array_filter($data, function($value) {
                return $value !== null && $value !== '';
            });

            $this->editingTask->update($data);

            // Dispatch global toast
            $this->dispatch('toast', type: 'success', title: 'Task Updated', message: 'Task updated successfully!', duration: 4000);
            
            // Close modal and reset form
            $this->closeEditModal();
            
            // Dispatch events for other components to update
            $this->dispatch('task-updated');
            $this->dispatch('refresh-task-list');
            
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', title: 'Update Failed', message: 'Unable to update task: ' . $e->getMessage(), duration: 6000);
            // Don't close modal on error, let user try again
        }
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
    $this->dispatch('toast', type: 'success', title: 'Task Deleted', message: 'Task deleted successfully!', duration: 4000);
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
            $this->dispatch('toast', type: 'success', title: 'Task Reopened', message: 'Task marked as pending.', duration: 3000);
        } else {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            $this->dispatch('toast', type: 'success', title: 'Task Completed', message: 'Task marked as completed.', duration: 3000);
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
