<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class TenantRouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();
        
        // Register tenant routes in the register method to ensure they're loaded early
        $this->mapTenantRoutes();
    }

    /**
     * Define the "tenant" routes for the application.
     *
     * These routes are loaded by the TenantRouteServiceProvider and all of them will
     * be assigned to the "web" middleware group and tenant-specific middleware.
     */
    protected function mapTenantRoutes(): void
    {
        Route::middleware([
            'web',
            InitializeTenancyByDomain::class,
            PreventAccessFromCentralDomains::class,
            'ensure.tenant.user',
        ])
        ->group(base_path('routes/tenant.php'));
    }
}
