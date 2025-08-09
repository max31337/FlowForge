<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TenantController extends Controller
{
    /**
     * Ensure the user has central admin access
     */
    private function ensureCentralAdmin()
    {
        if (!auth()->user() || !auth()->user()->hasRole('central_admin')) {
            abort(403, 'Access denied. Central admin privileges required.');
        }
    }

    /**
     * Display a listing of tenants.
     */
    public function index(): View
    {
        $this->ensureCentralAdmin();
        
        $tenants = Tenant::with('domains')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create(): View
    {
        $this->ensureCentralAdmin();
        return view('admin.tenants.create');
    }

    /**
     * Store a newly created tenant in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->ensureCentralAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tenants,slug',
            'email' => 'nullable|email|max:255',
            'domain' => 'nullable|string|max:255',
            'plan' => 'required|string|in:free,pro,enterprise',
        ]);

        // Generate slug if not provided
        $slug = $request->slug ?: Str::slug($request->name);
        
        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Create tenant
        $tenant = Tenant::create([
            'name' => $request->name,
            'slug' => $slug,
            'email' => $request->email,
            'plan' => $request->plan,
            'active' => true,
        ]);

        // Create domain if provided
        if ($request->domain) {
            $tenant->domains()->create([
                'domain' => $request->domain,
            ]);
        } else {
            // Create default domain based on slug
            $tenant->domains()->create([
                'domain' => $slug . '.localhost',
            ]);
        }

        return redirect()->route('admin.tenants.index')
            ->with('success', "Tenant '{$tenant->name}' created successfully!");
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant): View
    {
        $this->ensureCentralAdmin();
        
        $tenant->load(['domains', 'users' => function ($query) {
            $query->limit(10)->orderBy('created_at', 'desc');
        }]);

        $stats = [
            'total_users' => User::where('tenant_id', $tenant->id)->count(),
            'active_users' => User::where('tenant_id', $tenant->id)
                ->whereNotNull('email_verified_at')
                ->count(),
            'domains_count' => $tenant->domains->count(),
        ];

        return view('admin.tenants.show', compact('tenant', 'stats'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Tenant $tenant): View
    {
        $this->ensureCentralAdmin();
        $tenant->load('domains');
        return view('admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->ensureCentralAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . $tenant->id,
            'email' => 'nullable|email|max:255',
            'plan' => 'required|string|in:free,pro,enterprise',
            'active' => 'boolean',
        ]);

        $tenant->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'email' => $request->email,
            'plan' => $request->plan,
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', "Tenant '{$tenant->name}' updated successfully!");
    }

    /**
     * Remove the specified tenant from storage.
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $this->ensureCentralAdmin();
        
        $tenantName = $tenant->name;
        
        // This will trigger the TenantDeleted event which will delete the database
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', "Tenant '{$tenantName}' deleted successfully!");
    }

    /**
     * Toggle tenant active status.
     */
    public function toggleStatus(Tenant $tenant): RedirectResponse
    {
        $this->ensureCentralAdmin();
        
        $tenant->update(['active' => !$tenant->active]);

        $status = $tenant->active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Tenant '{$tenant->name}' {$status} successfully!");
    }
}
