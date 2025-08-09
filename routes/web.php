<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Main Route Orchestrator
|--------------------------------------------------------------------------
|
| This file orchestrates between central and tenant routes based on
| the domain being accessed. It ensures clean separation between
| central admin functionality and tenant-specific routes.
|
| ARCHITECTURE:
| - Central domains (localhost, 127.0.0.1): Load central.php routes only
| - Tenant domains (*.localhost): Tenant routes loaded by TenancyServiceProvider
|
*/

// Get domain information
$centralDomains = config('tenancy.central_domains', ['127.0.0.1', 'localhost']);
$host = request()->getHost();
$isOnCentralDomain = in_array($host, $centralDomains);

if ($isOnCentralDomain) {
    /*
    |--------------------------------------------------------------------------
    | Central Domain Routes
    |--------------------------------------------------------------------------
    | Only load central admin routes when accessing central domains.
    | This ensures tenant routes are never loaded on central domains.
    */
    require __DIR__.'/central.php';
    
} else {
    /*
    |--------------------------------------------------------------------------
    | Tenant Domain Routes  
    |--------------------------------------------------------------------------
    | For tenant domains, routes are handled by TenancyServiceProvider.
    | We only add fallback handling here for invalid tenant domains.
    */
    
    // Fallback for invalid tenant domains
    Route::fallback(function () {
        if (!tenancy()->initialized) {
            return redirect('/')->with('error', 'Organization not found. Please check the domain.');
        }
        abort(404);
    });
}
