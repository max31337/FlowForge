<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventTenantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $centralDomains = config('tenancy.central_domains', ['127.0.0.1', 'localhost']);
        $host = $request->getHost();
        
        // Only allow access if we're on a central domain
        if (!in_array($host, $centralDomains)) {
            // If accessed from tenant domain, redirect to tenant login or 404
            if (tenancy()->initialized) {
                return redirect()->route('login')
                    ->with('error', 'This area is only accessible from the main domain.');
            }
            abort(404);
        }
        
        return $next($request);
    }
}
