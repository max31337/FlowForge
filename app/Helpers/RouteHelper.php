<?php

if (!function_exists('dashboard_route')) {
    /**
     * Get the appropriate dashboard route based on context.
     */
    function dashboard_route(): string
    {
        // Check if we're in a tenant context (tenant domain)
        if (tenancy()->initialized) {
            try {
                return route('tenant.dashboard');
            } catch (\Exception $e) {
                // If tenant.dashboard route doesn't exist, return a fallback URL
                return '/dashboard';
            }
        }
        
        // Check if user is a central admin
        $user = auth()->user();
        if ($user && $user->hasRole('central_admin')) {
            return route('admin.dashboard');
        }
        
        // Check if we're on central domain and user has tenant_id
        $centralDomains = config('tenancy.central_domains', ['127.0.0.1', 'localhost']);
        $host = request()->getHost();
        $isOnCentralDomain = in_array($host, $centralDomains);
        
        if ($isOnCentralDomain) {
            // On central domain - redirect to admin if central admin, otherwise tenant
            if ($user && $user->hasRole('central_admin')) {
                return route('admin.dashboard');
            }
            // For tenant users on central domain, redirect to their tenant domain
            if ($user && $user->getAttribute('tenant_id')) {
                $tenant = \App\Models\Tenant::find($user->getAttribute('tenant_id'));
                if ($tenant && $tenant->domains->first()) {
                    $domainName = $tenant->domains->first()->getAttribute('domain');
                    return "http://{$domainName}:8000/dashboard";
                }
            }
            return route('admin.dashboard');
        }
        
        // On tenant domain - use tenant dashboard
        try {
            return route('tenant.dashboard');
        } catch (\Exception $e) {
            // If tenant.dashboard route doesn't exist, return a fallback URL
            return '/dashboard';
        }
    }
}

if (!function_exists('dashboard_route_name')) {
    /**
     * Get the appropriate dashboard route name based on context.
     */
    function dashboard_route_name(): string
    {
        // Check if we're in a tenant context
        if (tenancy()->initialized) {
            return 'tenant.dashboard';
        }
        
        // Check if user is a central admin
        $user = auth()->user();
        if ($user && $user->hasRole('central_admin')) {
            return 'admin.dashboard';
        }
        
        // Check domain context
        $centralDomains = config('tenancy.central_domains', ['127.0.0.1', 'localhost']);
        $host = request()->getHost();
        $isOnCentralDomain = in_array($host, $centralDomains);
        
        if ($isOnCentralDomain) {
            return 'admin.dashboard';
        }
        
        // Default to tenant dashboard on tenant domains
        return 'tenant.dashboard';
    }
}
