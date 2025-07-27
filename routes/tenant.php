<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider and all of them
| will be assigned to the "web" middleware group and tenant-specific middleware.
|
*/

// Debug route to test tenancy initialization
Route::get('/debug-tenancy', function () {
    $tenancyInitialized = tenancy()->initialized;
    $tenant = $tenancyInitialized ? tenancy()->tenant : null;
    return response()->json([
        'tenancy_initialized' => $tenancyInitialized,
        'tenant' => $tenant ? [
            'id' => $tenant->getKey(),
            'name' => $tenant->name ?? 'No name'
        ] : null,
        'current_host' => request()->getHost(),
        'user_authenticated' => auth()->check(),
        'user' => auth()->user() ? [
            'id' => auth()->user()->getKey(),
            'name' => auth()->user()->name,
            'tenant_id' => auth()->user()->tenant_id ?? 'no tenant_id'
        ] : null
    ]);
});

// Tenant welcome route
Route::get('/', function () {
    return view('tenant.welcome');
})->name('tenant.welcome');

// Tenant authentication routes (register, login, etc.)
require __DIR__.'/auth.php';

// Authenticated tenant routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Tenant Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');
    
    // User management routes (requires manage_users permission)
    Route::middleware('permission:manage_users')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('tenant.users.index');
        Route::get('/users/{user}/edit-role', [UserManagementController::class, 'editRole'])->name('tenant.users.edit-role');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('tenant.users.update-role');
    });
    
    // Project management routes (requires manage_projects permission)
    Route::middleware('permission:manage_projects')->group(function () {
        Route::get('/projects', function () {
            return view('tenant.projects', [
                'title' => 'Projects'
            ]);
        })->name('tenant.projects.index');
    });
    
    // Task management routes (requires manage_tasks permission) 
    Route::middleware('permission:manage_tasks')->group(function () {
        Route::get('/tasks', function () {
            return view('tenant.tasks', [
                'title' => 'Tasks'
            ]);
        })->name('tenant.tasks.index');
    });
    
    // Admin-only routes (requires owner or admin role)
    Route::middleware('role:owner,admin')->group(function () {
        // Tenant settings, billing, etc.
    });
});
