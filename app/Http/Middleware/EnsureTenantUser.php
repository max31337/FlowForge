<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantUser
{
    /**
     * Handle an incoming request.
     *
     * Ensure that authenticated users belong to the current tenant.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check if we're in a tenant context and user is authenticated
        if (tenancy()->initialized && Auth::check()) {
            $user = Auth::user();
            $currentTenantId = tenant('id');
            
            // Check if user belongs to current tenant
            if ($user->tenant_id !== $currentTenantId) {
                // User doesn't belong to this tenant, log them out
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'You do not have access to this organization.');
            }
        }

        return $next($request);
    }
}
