<?php

namespace App\Livewire\Tenant;

use App\Models\Project;
use App\Livewire\TenantAwareComponent;
use Livewire\Attributes\Computed;

class RecentProjects extends TenantAwareComponent
{
    public int $limit = 5;
    public bool $showAll = false;

    #[Computed]
    public function projects()
    {
        $tenantId = $this->getTenantId();
        
        if (!$tenantId) {
            return collect();
        }

        $query = Project::where('tenant_id', $tenantId)
            ->with(['tasks' => function($query) {
                $query->select('project_id', 'status');
            }])
            ->orderBy('updated_at', 'desc');

        if (!$this->showAll) {
            $query->limit($this->limit);
        }

        return $query->get()->map(function ($project) {
            $taskCounts = $project->tasks->countBy('status');
            
            return (object) [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'status' => $project->status,
                'updated_at' => $project->updated_at,
                'total_tasks' => $project->tasks->count(),
                'completed_tasks' => $taskCounts['completed'] ?? 0,
                'progress' => $project->tasks->count() > 0 
                    ? round((($taskCounts['completed'] ?? 0) / $project->tasks->count()) * 100, 1)
                    : 0,
            ];
        });
    }

    public function toggleShowAll()
    {
        $this->showAll = !$this->showAll;
        unset($this->projects);
    }

    public function render()
    {
        return view('livewire.tenant.recent-projects');
    }
}
