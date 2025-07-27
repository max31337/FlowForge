<?php

require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap the Laravel application
$app = require_once __DIR__.'/bootstrap/app.php';

// Create request for a tenant domain
$request = Request::create('http://techstart.localhost:8000/dashboard', 'GET');
$app->instance('request', $request);

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== ROUTE REGISTRATION DEBUG ===\n";

// Test with different domains
$domains = [
    'localhost:8000',
    'techstart.localhost:8000'
];

foreach ($domains as $domain) {
    echo "\n🔍 Testing domain: {$domain}\n";
    
    // Create request for this domain
    $testRequest = Request::create("http://{$domain}/dashboard", 'GET');
    $app->instance('request', $testRequest);
    
    try {
        // Boot the kernel to register routes
        $response = $kernel->handle($testRequest);
        
        echo "   ✅ Request handled successfully (status: {$response->getStatusCode()})\n";
        
        // Check route collection
        $router = app('router');
        $routes = $router->getRoutes();
        
        echo "   📊 Total routes registered: " . count($routes->getRoutes()) . "\n";
        
        // Look for tenant.dashboard specifically
        $tenantDashboardFound = false;
        foreach ($routes as $route) {
            if ($route->getName() === 'tenant.dashboard') {
                $tenantDashboardFound = true;
                echo "   ✅ tenant.dashboard route found!\n";
                echo "      URI: " . $route->uri() . "\n";
                echo "      Methods: " . implode('|', $route->methods()) . "\n";
                echo "      Action: " . $route->getActionName() . "\n";
                echo "      Middleware: " . implode(', ', $route->middleware()) . "\n";
                break;
            }
        }
        
        if (!$tenantDashboardFound) {
            echo "   ❌ tenant.dashboard route NOT found\n";
            
            // List all dashboard-related routes
            $dashboardRoutes = [];
            foreach ($routes as $route) {
                if (str_contains($route->getName() ?? '', 'dashboard') || str_contains($route->uri(), 'dashboard')) {
                    $dashboardRoutes[] = [
                        'name' => $route->getName(),
                        'uri' => $route->uri(),
                        'methods' => implode('|', $route->methods()),
                        'middleware' => implode(', ', $route->middleware())
                    ];
                }
            }
            
            if (!empty($dashboardRoutes)) {
                echo "   📋 Dashboard-related routes found:\n";
                foreach ($dashboardRoutes as $route) {
                    echo "      - {$route['name']} => {$route['uri']} [{$route['methods']}] (middleware: {$route['middleware']})\n";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "   ❌ Error handling request: " . $e->getMessage() . "\n";
        echo "   📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}

// Test tenancy initialization directly
echo "\n🏢 Testing tenancy initialization:\n";

try {
    // Try to initialize tenancy for techstart domain
    $domain = \App\Models\Domain::where('domain', 'techstart.localhost')->first();
    if ($domain) {
        echo "   ✅ Domain found in database: {$domain->domain}\n";
        echo "   🏢 Associated tenant: {$domain->tenant->getKey()}\n";
        
        // Initialize tenancy
        tenancy()->initialize($domain->tenant);
        echo "   ✅ Tenancy initialized successfully\n";
        
        // Now check routes again
        $router = app('router');
        $routes = $router->getRoutes();
        
        $tenantDashboardFound = false;
        foreach ($routes as $route) {
            if ($route->getName() === 'tenant.dashboard') {
                $tenantDashboardFound = true;
                echo "   ✅ tenant.dashboard route found after tenancy init!\n";
                break;
            }
        }
        
        if (!$tenantDashboardFound) {
            echo "   ❌ tenant.dashboard route STILL not found after tenancy init\n";
        }
        
    } else {
        echo "   ❌ Domain 'techstart.localhost' not found in database\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error initializing tenancy: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
