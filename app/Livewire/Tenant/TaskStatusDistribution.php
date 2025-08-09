<?php

namespace App\Livewire\Tenant;

use App\Livewire\TenantAwareComponent;
use App\Models\Task;
use Livewire\Attributes\Computed;

class TaskStatusDistribution extends TenantAwareComponent
{
    #[Computed]
    public function counts(): array
    {
        $tenantId = $this->getTenantId();
        if (!$tenantId) {
            return [
                'total' => 0,
                'pending' => 0,
                'in_progress' => 0,
                'review' => 0,
                'completed' => 0,
            ];
        }

        $rows = Task::where('tenant_id', $tenantId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $counts = [
            'pending' => $rows['pending'] ?? 0,
            'in_progress' => $rows['in_progress'] ?? 0,
            'review' => $rows['review'] ?? 0,
            'completed' => $rows['completed'] ?? 0,
        ];

        $counts['total'] = array_sum($counts);
        return $counts;
    }

    public function render()
    {
        return view('livewire.tenant.task-status-distribution');
    }
}
