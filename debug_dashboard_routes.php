<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TENANT DASHBOARD ROUTE SPECIFIC DEBUG ===\n\n";

// 1. List ALL routes and look for dashboard specifically
echo "🔍 Searching for dashboard routes:\n";
$router = app('router');
$routes = $router->getRoutes();

$dashboardRoutes = [];
foreach ($routes as $route) {
    $routeName = $route->getName();
    $uri = $route->uri();
    
    if ($routeName && (str_contains($routeName, 'dashboard') || str_contains($uri, 'dashboard'))) {
        $methods = implode('|', $route->methods());
        $middleware = implode(', ', $route->gatherMiddleware());
        $dashboardRoutes[] = "{$routeName} => {$uri} [{$methods}] (middleware: {$middleware})";
    }
}

if (empty($dashboardRoutes)) {
    echo "❌ NO dashboard routes found at all!\n";
} else {
    foreach ($dashboardRoutes as $route) {
        echo "   ✅ {$route}\n";
    }
}

echo "\n";

// 2. Check if tenant.dashboard specifically exists
echo "🎯 Checking for tenant.dashboard route specifically:\n";
try {
    $dashboardUrl = route('tenant.dashboard');
    echo "✅ tenant.dashboard route EXISTS!\n";
    echo "   URL: {$dashboardUrl}\n";
} catch (Exception $e) {
    echo "❌ tenant.dashboard route NOT FOUND!\n";
    echo "   Error: {$e->getMessage()}\n";
}

echo "\n";

// 3. Check middleware registration more carefully
echo "🛡️  Detailed middleware check:\n";
$app = app();
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Use reflection to get middleware
$reflection = new ReflectionClass($kernel);
if ($reflection->hasProperty('middlewareAliases') || $reflection->hasProperty('routeMiddleware')) {
    try {
        // Try different property names for different Laravel versions
        $property = null;
        if ($reflection->hasProperty('middlewareAliases')) {
            $property = $reflection->getProperty('middlewareAliases');
        } elseif ($reflection->hasProperty('routeMiddleware')) {
            $property = $reflection->getProperty('routeMiddleware');
        }
        
        if ($property) {
            $property->setAccessible(true);
            $middlewareAliases = $property->getValue($kernel);
            
            if (isset($middlewareAliases['ensure.tenant.user'])) {
                echo "✅ ensure.tenant.user middleware is registered: {$middlewareAliases['ensure.tenant.user']}\n";
            } else {
                echo "❌ ensure.tenant.user middleware NOT found in kernel!\n";
                echo "Available middleware aliases:\n";
                foreach (array_slice($middlewareAliases, 0, 10) as $alias => $class) {
                    echo "   - {$alias} => {$class}\n";
                }
            }
        }
    } catch (Exception $e) {
        echo "❌ Error checking middleware: {$e->getMessage()}\n";
    }
}

echo "\n";

// 4. Test tenant route loading with simulated tenant context
echo "🏢 Testing with tenant context:\n";
$tenant = App\Models\Tenant::where('slug', 'techstart')->first();
if ($tenant) {
    // Initialize tenant context
    tenancy()->initialize($tenant);
    
    echo "✅ Tenant initialized: {$tenant->getAttribute('name')}\n";
    
    // Try to access the route again
    try {
        $dashboardUrl = route('tenant.dashboard');
        echo "✅ tenant.dashboard route accessible with tenant context!\n";
        echo "   URL: {$dashboardUrl}\n";
    } catch (Exception $e) {
        echo "❌ tenant.dashboard route still not accessible with tenant context!\n";
        echo "   Error: {$e->getMessage()}\n";
    }
    
    tenancy()->end();
}

echo "\n";

// 5. Test URL generation manually
echo "🔧 Manual URL generation test:\n";
try {
    // Try to generate URL manually
    $router = app('router');
    $routes = $router->getRoutes();
    
    $tenantDashboardRoute = $routes->getByName('tenant.dashboard');
    if ($tenantDashboardRoute) {
        echo "✅ Found tenant.dashboard route object\n";
        echo "   URI: {$tenantDashboardRoute->uri()}\n";
        echo "   Methods: " . implode('|', $tenantDashboardRoute->methods()) . "\n";
        echo "   Action: " . $tenantDashboardRoute->getActionName() . "\n";
    } else {
        echo "❌ tenant.dashboard route object NOT found\n";
    }
} catch (Exception $e) {
    echo "❌ Error in manual route check: {$e->getMessage()}\n";
}

echo "\n=== SUMMARY ===\n";
if (empty($dashboardRoutes)) {
    echo "🚨 CRITICAL: No dashboard routes found at all!\n";
    echo "   This suggests routes are not loading properly.\n";
} else {
    echo "📊 Dashboard routes found but tenant.dashboard may have issues.\n";
}
