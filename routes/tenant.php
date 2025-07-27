<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\UserManagementController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'ensure.tenant.user',
])->group(function () {
    // Only register tenant routes if we're actually on a tenant domain
    // and tenancy is properly initialized
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
            // Add project management routes here
        });
        
        // Task management routes (requires manage_tasks permission) 
        Route::middleware('permission:manage_tasks')->group(function () {
            // Add task management routes here
        });
        
        // Admin-only routes (requires owner or admin role)
        Route::middleware('role:owner,admin')->group(function () {
            // Tenant settings, billing, etc.
        });
    });
});
