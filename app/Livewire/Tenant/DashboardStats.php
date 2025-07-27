<?php

namespace App\Livewire\Tenant;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Livewire\TenantAwareComponent;
use Livewire\Attributes\Computed;

class DashboardStats extends TenantAwareComponent
{
    public bool $loading = true;

    public function mount()
    {
        // Simulate loading for better UX
        $this->loading = false;
    }

    #[Computed]
    public function stats()
    {
        $tenantId = $this->getTenantId();
        
        if (!$tenantId) {
            return [
                'total_projects' => 0,
                'total_tasks' => 0,
                'pending_tasks' => 0,
                'in_progress_tasks' => 0,
                'completed_tasks' => 0,
                'total_users' => 0,
                'completion_rate' => 0,
                'debug_info' => 'No tenant context',
            ];
        }
        
        // Debug logging
        logger()->info('DashboardStats: Loading stats for tenant', [
            'tenant_id' => $tenantId,
            'tenant_name' => $this->safeTenant('name') ?? 'Unknown'
        ]);
        
        $totalTasks = Task::where('tenant_id', $tenantId)->count();
        $completedTasks = Task::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->count();

        $stats = [
            'total_projects' => Project::where('tenant_id', $tenantId)->count(),
            'total_tasks' => $totalTasks,
            'pending_tasks' => Task::where('tenant_id', $tenantId)
                ->where('status', 'pending')
                ->count(),
            'in_progress_tasks' => Task::where('tenant_id', $tenantId)
                ->where('status', 'in_progress')
                ->count(),
            'completed_tasks' => $completedTasks,
            'total_users' => User::where('tenant_id', $tenantId)->count(),
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
            'debug_info' => "Tenant: {$tenantId}",
        ];

        logger()->info('DashboardStats: Loaded stats', $stats);

        return $stats;
    }

    public function refresh()
    {
        $this->loading = true;
        
        // Simulate a small delay for better UX
        sleep(1);
        
        $this->loading = false;
        
        // Clear computed property cache
        unset($this->stats);
        
        $this->dispatch('stats-refreshed');
    }

    public function render()
    {
        return view('livewire.tenant.dashboard-stats');
    }
}
