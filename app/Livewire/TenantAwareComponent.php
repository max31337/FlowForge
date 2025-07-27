<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

abstract class TenantAwareComponent extends Component
{
    /**
     * Ensure tenant context is initialized before any component actions.
     */
    public function hydrate()
    {
        // This runs before every request to the component
        $this->ensureTenantContext();
    }

    /**
     * Ensure tenant context is available when component boots.
     */
    public function boot()
    {
        $this->ensureTenantContext();
    }

    /**
     * Ensure tenant context is properly initialized.
     */
    protected function ensureTenantContext()
    {
        if (!tenancy()->initialized) {
            Log::warning('Tenant context not initialized in Livewire component', [
                'component' => static::class,
                'url' => request()->url(),
                'host' => request()->getHost(),
                'user_agent' => request()->userAgent(),
            ]);
            
            // Try to initialize tenancy based on current request
            $this->attemptTenantInitialization();
        }
    }

    /**
     * Attempt to initialize tenant context if it's missing.
     */
    protected function attemptTenantInitialization()
    {
        try {
            $host = request()->getHost();
            
            // Try to find tenant by domain
            $tenant = \App\Models\Tenant::whereHas('domains', function($query) use ($host) {
                $query->where('domain', $host);
            })->first();
            
            if ($tenant) {
                tenancy()->initialize($tenant);
                Log::info('Successfully initialized tenant context in Livewire', [
                    'tenant_id' => $tenant->id,
                    'host' => $host,
                    'component' => static::class,
                ]);
            } else {
                Log::error('Could not find tenant for domain in Livewire component', [
                    'host' => $host,
                    'component' => static::class,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize tenant context in Livewire component', [
                'error' => $e->getMessage(),
                'component' => static::class,
                'host' => request()->getHost(),
            ]);
        }
    }

    /**
     * Get current tenant ID safely.
     */
    protected function getTenantId()
    {
        $this->ensureTenantContext();
        
        if (!tenancy()->initialized) {
            return null;
        }
        
        return tenant('id');
    }

    /**
     * Check if tenant context is available and throw exception if not.
     */
    protected function requireTenantContext()
    {
        $this->ensureTenantContext();
        
        if (!tenancy()->initialized || !tenant('id')) {
            throw new \RuntimeException('Tenant context is required but not available.');
        }
    }

    /**
     * Safe tenant() helper that ensures context is available.
     */
    protected function safeTenant($key = null)
    {
        $this->ensureTenantContext();
        
        if (!tenancy()->initialized) {
            return null;
        }
        
        return tenant($key);
    }
}
