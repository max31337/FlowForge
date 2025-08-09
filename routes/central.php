<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central Application Routes
|--------------------------------------------------------------------------
|
| These routes are for the central application (main domain only).
| They handle super admin functionality and tenant management.
|
*/

// Central welcome route
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Central admin routes - protected from tenant domains
Route::middleware([
    'prevent.tenant.access',
    'auth',
    'verified'
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tenant management
    Route::resource('tenants', TenantController::class);
    Route::patch('tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])
        ->name('tenants.toggle-status');
});

// Profile routes for central users
Route::middleware(['auth', 'prevent.tenant.access'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Central authentication routes
require __DIR__.'/auth.php';
