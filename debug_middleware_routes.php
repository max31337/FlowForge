<?php

require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap the Laravel application
$app = require_once __DIR__.'/bootstrap/app.php';

echo "=== MIDDLEWARE AND ROUTE LOADING DEBUG ===\n";

// Test middleware registration
echo "\n🛡️  Checking middleware registration:\n";

$middleware = [
    'ensure.tenant.user' => \App\Http\Middleware\EnsureTenantUser::class,
    'prevent.tenant.access' => \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
    'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
];

foreach ($middleware as $alias => $class) {
    try {
        $resolved = $app['router']->getMiddleware()[$alias] ?? null;
        if ($resolved) {
            echo "   ✅ {$alias} => {$resolved}\n";
        } else {
            echo "   ❌ {$alias} => NOT REGISTERED\n";
        }
    } catch (Exception $e) {
        echo "   ❌ {$alias} => ERROR: {$e->getMessage()}\n";
    }
}

// Test tenancy service provider
echo "\n🏢 Testing TenancyServiceProvider:\n";

try {
    $provider = $app->getProvider(\App\Providers\TenancyServiceProvider::class);
    if ($provider) {
        echo "   ✅ TenancyServiceProvider is registered\n";
        
        // Check if boot method was called
        $reflection = new ReflectionClass($provider);
        echo "   📋 TenancyServiceProvider methods: " . implode(', ', array_map(fn($m) => $m->getName(), $reflection->getMethods())) . "\n";
    } else {
        echo "   ❌ TenancyServiceProvider is NOT registered\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking TenancyServiceProvider: " . $e->getMessage() . "\n";
}

// Test route file existence and content
echo "\n📁 Testing route files:\n";

$routeFiles = [
    'web.php' => base_path('routes/web.php'),
    'tenant.php' => base_path('routes/tenant.php'),
    'auth.php' => base_path('routes/auth.php'),
];

foreach ($routeFiles as $name => $path) {
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $lines = substr_count($content, "\n") + 1;
        echo "   ✅ {$name} exists ({$lines} lines)\n";
        
        if ($name === 'tenant.php') {
            // Check for tenant.dashboard route definition
            if (str_contains($content, "name('tenant.dashboard')")) {
                echo "      ✅ Contains tenant.dashboard route definition\n";
            } else {
                echo "      ❌ Does NOT contain tenant.dashboard route definition\n";
            }
        }
    } else {
        echo "   ❌ {$name} does NOT exist at {$path}\n";
    }
}

// Simulate tenant request and check route registration
echo "\n🌐 Simulating tenant request:\n";

try {
    // Create a request to tenant domain
    $request = Request::create('http://techstart.localhost:8000/dashboard', 'GET');
    $app->instance('request', $request);
    
    // Get the HTTP kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    echo "   📨 Created request for techstart.localhost:8000/dashboard\n";
    
    // Try to match the route
    try {
        $router = $app['router'];
        $route = $router->getRoutes()->match($request);
        echo "   ✅ Route matched: " . ($route->getName() ?? 'unnamed') . "\n";
        echo "      URI pattern: " . $route->uri() . "\n";
        echo "      Action: " . $route->getActionName() . "\n";
        echo "      Middleware stack: " . implode(' -> ', $route->middleware()) . "\n";
    } catch (Exception $e) {
        echo "   ❌ Route matching failed: " . $e->getMessage() . "\n";
        
        // Check if we have any routes at all
        $allRoutes = $router->getRoutes()->getRoutes();
        echo "   📊 Total routes available: " . count($allRoutes) . "\n";
        
        // Look for any tenant-related routes
        $tenantRoutes = array_filter($allRoutes, function($route) {
            return str_contains($route->getName() ?? '', 'tenant') || 
                   str_contains($route->uri(), 'tenant') ||
                   in_array('Stancl\Tenancy\Middleware\InitializeTenancyByDomain', $route->middleware());
        });
        
        echo "   🏢 Tenant-related routes found: " . count($tenantRoutes) . "\n";
        foreach (array_slice($tenantRoutes, 0, 5) as $route) {
            echo "      - " . ($route->getName() ?? 'unnamed') . " => " . $route->uri() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ Error simulating request: " . $e->getMessage() . "\n";
    echo "   📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== END DEBUG ===\n";
