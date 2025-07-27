<?php

namespace App\Livewire\Tenant\Tasks;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Livewire\TenantAwareComponent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class CreateTaskForm extends TenantAwareComponent
{
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

    public $showModal = false;

    protected $listeners = [
        'open-task-modal' => 'openModal',
        'close-task-modal' => 'closeModal',
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

    public function openModal()
    {
        $this->resetTaskForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetTaskForm();
        $this->resetValidation();
        
        // Clear any error messages
        session()->forget(['message', 'error']);
        
        // Force refresh the component state
        $this->dispatch('$refresh');
    }

    public function createTask()
    {   
        try {
            $this->validate();

            $data = $this->taskForm;
            $data['created_by'] = Auth::id();
            
            // Get tenant_id using the TenantAwareComponent method
            $tenantId = $this->getTenantId();
            if (!$tenantId) {
                // Fallback: try to get tenant from current domain
                $host = request()->getHost();
                $tenant = \App\Models\Tenant::whereHas('domains', function($query) use ($host) {
                    $query->where('domain', $host);
                })->first();
                
                if ($tenant) {
                    $tenantId = $tenant->id;
                } else {
                    throw new \Exception('Unable to determine tenant context');
                }
            }
            
            $data['tenant_id'] = $tenantId;
            
            // Remove empty values (but keep tenant_id and created_by)
            $data = array_filter($data, function($value, $key) {
                return in_array($key, ['tenant_id', 'created_by']) || ($value !== null && $value !== '');
            }, ARRAY_FILTER_USE_BOTH);

            Task::create($data);

            // Flash success message first
            session()->flash('message', 'Task created successfully!');
            
            // Close modal and reset form
            $this->closeModal();
            
            // Dispatch events for other components to update
            $this->dispatch('task-created');
            $this->dispatch('refresh-task-list');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to create task: ' . $e->getMessage());
            // Don't close modal on error, let user try again
        }
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
    public function projects()
    {
        // Use TenantAwareComponent method for safe tenant access
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
        // Use TenantAwareComponent method for safe tenant access
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
        // Use TenantAwareComponent method for safe tenant access
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
        return view('livewire.tenant.tasks.create-task-form');
    }
}
