<?php

namespace App\Livewire\Tenant\Projects;

use App\Models\Project;
use App\Models\Category;
use App\Livewire\TenantAwareComponent;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class ProjectList extends TenantAwareComponent
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $priorityFilter = '';
    public $showInactive = false;
    public $perPage = 10;

    // Project editing properties
    public $showEditModal = false;
    public $editingProject = null;
    
    public $projectForm = [
        'name' => '',
        'description' => '',
        'status' => 'planning',
        'priority' => 'medium',
        'start_date' => null,
        'due_date' => null,
        'budget' => null,
        'category_id' => null,
    ];

    protected $listeners = [
        // React to external events
        'refresh-project-list' => '$refresh',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'showInactive' => ['except' => false],
    ];

    protected function rules() 
    {
        $tenantId = $this->getTenantId();
        
        if (!$tenantId) {
            return [];
        }
        
        return [
            'projectForm.name' => 'required|string|max:255',
            'projectForm.description' => 'nullable|string',
            'projectForm.status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'projectForm.priority' => 'required|in:low,medium,high,urgent',
            'projectForm.start_date' => 'nullable|date',
            'projectForm.due_date' => 'nullable|date|after:projectForm.start_date',
            'projectForm.budget' => 'nullable|numeric|min:0',
            'projectForm.category_id' => 'nullable|exists:categories,id,tenant_id,' . $tenantId,
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

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function toggleInactive()
    {
        $this->showInactive = !$this->showInactive;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'categoryFilter', 'priorityFilter']);
        $this->showInactive = false;
        $this->resetPage();
    }

    // Create flow moved to CreateProjectForm component

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingProject = null;
        $this->resetProjectForm();
        $this->resetValidation();
        
        // Clear any error messages
        session()->forget(['error']);
        
        // Force refresh the component state
        $this->dispatch('$refresh');
    }

    // createProject removed

    public function updateProject()
    {
        try {
            $this->requireTenantContext();
            
            if (!$this->editingProject) {
                throw new \RuntimeException('No project selected for editing.');
            }

            $this->validate();

            $data = $this->projectForm;
            
            // Remove empty values
            $data = array_filter($data, function($value) {
                return $value !== null && $value !== '';
            });

            $this->editingProject->update($data);

            // Global toast (top-right)
            $this->dispatch('toast', type: 'success', title: 'Success', message: 'Project updated successfully!', duration: 5000);
            
            // Close modal and reset form
            $this->closeEditModal();
            
            // Dispatch events for other components to update
            $this->dispatch('project-updated');
            $this->dispatch('refresh-project-list');
            
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Unable to update project: ' . $e->getMessage(), duration: 6000);
            // Don't close modal on error, let user try again
        }
    }

    public function deleteProject($projectId)
    {
        try {
            $this->requireTenantContext();
            
            $project = Project::where('tenant_id', $this->getTenantId())->findOrFail($projectId);
            
            // Check if project has tasks
            if ($project->tasks()->count() > 0) {
                $this->dispatch('toast', type: 'error', title: 'Action blocked', message: 'Cannot delete project with existing tasks.', duration: 6000);
                return;
            }
            
            $project->delete();
            
            $this->dispatch('project-deleted');
            $this->dispatch('toast', type: 'success', title: 'Deleted', message: 'Project deleted successfully!', duration: 5000);
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Unable to delete project: ' . $e->getMessage(), duration: 6000);
        }
    }

    public function toggleProjectStatus($projectId)
    {
        // Ensure tenant context and project belongs to current tenant
        if (!tenancy()->initialized) {
            return;
        }

        $project = Project::where('tenant_id', tenant('id'))->findOrFail($projectId);
        
        if ($project->status === 'active') {
            $project->update(['status' => 'on_hold']);
        } else if ($project->status === 'on_hold') {
            $project->update(['status' => 'active']);
        } else {
            $project->update(['status' => 'active']);
        }

        $this->dispatch('project-status-changed');
    }

    private function resetProjectForm()
    {
        $this->projectForm = [
            'name' => '',
            'description' => '',
            'status' => 'planning',
            'priority' => 'medium',
            'start_date' => null,
            'due_date' => null,
            'budget' => null,
            'category_id' => null,
        ];
    }

    #[Computed]
    public function projects()
    {
        // Ensure tenant context
        if (!tenancy()->initialized) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                [], 0, $this->perPage, 1, ['path' => request()->url()]
            );
        }

        $query = Project::with(['category', 'tasks'])
            ->withCount(['tasks', 'tasks as completed_tasks_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->where('tenant_id', tenant('id'))
            ->when($this->search, function($q) {
                $q->where(function($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->categoryFilter, function($q) {
                $q->where('category_id', $this->categoryFilter);
            })
            ->when($this->priorityFilter, function($q) {
                $q->where('priority', $this->priorityFilter);
            })
            ->when(!$this->showInactive, function($q) {
                $q->where('is_active', true);
            })
            ->orderBy('created_at', 'desc');

        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function categories()
    {
        // Ensure tenant context
        if (!tenancy()->initialized) {
            return collect();
        }

        return Category::where('tenant_id', tenant('id'))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function statusOptions()
    {
        return [
            'planning' => 'Planning',
            'active' => 'Active',
            'on_hold' => 'On Hold',
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
        return view('livewire.tenant.projects.project-list');
    }
}
