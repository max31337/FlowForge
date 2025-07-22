<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central Application Routes
|--------------------------------------------------------------------------
|
| These routes are for the central application (main domain).
| Tenant-specific routes are handled in routes/tenant.php.
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Central admin routes - protected from tenant domains
Route::middleware([
    'web',
    'prevent.tenant.access',
    'auth',
    'verified',
    'role:central_admin'
    
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tenant management
    Route::resource('tenants', TenantController::class);
    Route::patch('tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])
        ->name('tenants.toggle-status');
});

// Dashboard route - handles both central and tenant domain redirects
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();
    
    // Get host information to determine routing
    $centralDomains = config('tenancy.central_domains', ['127.0.0.1', 'localhost']);
    $host = request()->getHost();
    $isOnCentralDomain = in_array($host, $centralDomains);
    
    // If user is central admin, always redirect to admin dashboard
    if ($user->hasRole('central_admin')) {
        return redirect()->route('admin.dashboard');
    }
    
    // If we're on a tenant domain, redirect to tenant dashboard
    if (!$isOnCentralDomain && tenancy()->initialized) {
        // Verify user belongs to this tenant
        if ($user->getAttribute('tenant_id') === tenant('id')) {
            return redirect()->route('tenant.dashboard');
        } else {
            // User doesn't belong to this tenant - logout and redirect to login
            auth()->logout();
            return redirect()->route('login')->with('error', 'Access denied for this organization.');
        }
    }
    
    // If tenant user is on central domain, redirect to their tenant domain
    if ($isOnCentralDomain && $user->getAttribute('tenant_id')) {
        $tenant = \App\Models\Tenant::find($user->getAttribute('tenant_id'));
        if ($tenant && $tenant->domains->first()) {
            $domainName = $tenant->domains->first()->getAttribute('domain');
            return redirect("http://{$domainName}:8000/dashboard");
        }
    }
    
    // Default fallback - show generic dashboard
    return view('dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Central authentication routes
require __DIR__.'/auth.php';
