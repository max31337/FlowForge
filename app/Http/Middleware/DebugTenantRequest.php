<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DebugTenantRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('Tenant Request Debug', [
            'url' => $request->url(),
            'method' => $request->method(),
            'host' => $request->getHost(),
            'tenancy_initialized' => tenancy()->initialized,
            'tenant_id' => tenancy()->initialized ? tenancy()->tenant->getKey() : null,
            'session_id' => $request->session()->getId(),
            'csrf_token' => $request->session()->token(),
            'request_token' => $request->input('_token'),
            'request_data' => $request->method() === 'POST' ? $request->only(['email', '_token', 'password']) : [],
            'user_authenticated' => auth()->check(),
            'user_id' => auth()->check() ? auth()->user()->getKey() : null,
            'route_name' => $request->route() ? $request->route()->getName() : null,
            'middleware_stack' => $request->route() ? $request->route()->middleware() : [],
        ]);

        return $next($request);
    }
}
