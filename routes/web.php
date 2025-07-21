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

// For development/testing - remove in production
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Central authentication routes
require __DIR__.'/auth.php';
