<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        // Ensure user has central admin permissions
        if (!auth()->user() || !auth()->user()->hasRole('central_admin')) {
            abort(403, 'Access denied. Central admin privileges required.');
        }
        
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('active', true)->count(),
            'total_users' => User::count(),
            'recent_tenants' => Tenant::with('domains')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        $planStats = Tenant::selectRaw('plan, COUNT(*) as count')
            ->groupBy('plan')
            ->pluck('count', 'plan')
            ->toArray();

        return view('admin.dashboard', compact('stats', 'planStats'));
    }
}