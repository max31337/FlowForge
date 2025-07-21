<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the tenant dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_projects' => Project::where('tenant_id', tenant('id'))->count(),
            'total_tasks' => Task::where('tenant_id', tenant('id'))->count(),
            'pending_tasks' => Task::where('tenant_id', tenant('id'))
                ->where('status', 'pending')
                ->count(),
            'completed_tasks' => Task::where('tenant_id', tenant('id'))
                ->where('status', 'completed')
                ->count(),
            'total_users' => User::where('tenant_id', tenant('id'))->count(),
        ];

        $recentProjects = Project::where('tenant_id', tenant('id'))
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        $recentTasks = Task::where('tenant_id', tenant('id'))
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return view('tenant.dashboard', compact('stats', 'recentProjects', 'recentTasks'));
    }
}
