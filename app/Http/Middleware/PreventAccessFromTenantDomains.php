<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAccessFromTenantDomains
{
    /**
     * Handle an incoming request.
     *
     * Prevent access to central admin routes from tenant domains.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the central domains from tenancy config
        $centralDomains = config('tenancy.central_domains', ['127.0.0.1', 'localhost']);
        
        // Get the current host
        $host = $request->getHost();
        
        // Check if the current host is NOT a central domain
        if (!in_array($host, $centralDomains)) {
            // This request is coming from a tenant domain
            // Check if tenancy is initialized (meaning it's a valid tenant domain)
            if (tenancy()->initialized) {
                abort(404, 'Admin area not accessible from tenant domains.');
            }
        }

        return $next($request);
    }
}
