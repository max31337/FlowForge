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

    // Project creation/editing properties
    public $showCreateModal = false;
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
        'open-project-modal' => 'openCreateModal',
        'close-project-modal' => 'closeCreateModal',
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

    public function openCreateModal()
    {
        $this->resetProjectForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetProjectForm();
        $this->resetValidation();
        
        // Clear any error messages
        session()->forget(['error']);
        
        // Dispatch event to close modal via JavaScript
        $this->dispatch('close-modal');
    }

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

    public function createProject()
    {
        try {
            // Validate the form
            $this->validate();

            $data = $this->projectForm;
            
            // Get tenant_id directly - BelongsToTenant trait will handle this automatically
            // But let's explicitly set it to be sure
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
            
            // Remove empty values (but keep tenant_id)
            $data = array_filter($data, function($value, $key) {
                return $key === 'tenant_id' || ($value !== null && $value !== '');
            }, ARRAY_FILTER_USE_BOTH);

            Project::create($data);

            // Flash success message first
            session()->flash('message', 'Project created successfully!');
            
            // Close modal and reset form
            $this->closeCreateModal();
            
            // Dispatch events for other components to update
            $this->dispatch('project-created');
            $this->dispatch('refresh-project-list');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to show in UI
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to create project: ' . $e->getMessage());
            // Don't close modal on error, let user try again
        }
    }

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

            // Flash success message first
            session()->flash('message', 'Project updated successfully!');
            
            // Close modal and reset form
            $this->closeEditModal();
            
            // Dispatch events for other components to update
            $this->dispatch('project-updated');
            $this->dispatch('refresh-project-list');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to update project: ' . $e->getMessage());
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
                session()->flash('error', 'Cannot delete project with existing tasks.');
                return;
            }
            
            $project->delete();
            
            $this->dispatch('project-deleted');
            session()->flash('message', 'Project deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to delete project: ' . $e->getMessage());
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
