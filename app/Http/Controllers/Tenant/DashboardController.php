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
        // Debug: Check tenancy initialization
        if (!tenancy()->initialized) {
            logger()->error('Tenant dashboard accessed without tenancy initialized');
            abort(403, 'Tenant context not available');
        }

        // Debug: Log tenant info
        logger()->info('Tenant dashboard accessed', [
            'tenant_id' => tenant('id'),
            'tenant_name' => tenancy()->tenant->getAttribute('name'),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
        ]);

        // All data is now handled by Livewire components
        // which ensures proper tenant isolation and real-time updates
        return view('tenant.dashboard');
    }
}
