<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== COMPREHENSIVE TENANT ROUTING DEBUG ===\n\n";

// 1. Check if tenant routes are being registered at all
echo "🛣️  STEP 1: Route Registration Analysis\n";
echo "================================================\n";

$router = app('router');
$routes = $router->getRoutes();

$centralRoutes = [];
$tenantRoutes = [];
$allRouteNames = [];

foreach ($routes as $route) {
    $routeName = $route->getName();
    $allRouteNames[] = $routeName ?? 'unnamed';
    
    if ($routeName) {
        if (str_contains($routeName, 'admin.')) {
            $centralRoutes[] = $routeName . ' => ' . $route->uri() . ' [' . implode('|', $route->methods()) . ']';
        } elseif (str_contains($routeName, 'tenant.')) {
            $tenantRoutes[] = $routeName . ' => ' . $route->uri() . ' [' . implode('|', $route->methods()) . ']';
        }
    }
}

echo "📊 Route Statistics:\n";
echo "   Total routes: " . count($routes) . "\n";
echo "   Central routes: " . count($centralRoutes) . "\n";
echo "   Tenant routes: " . count($tenantRoutes) . "\n\n";

echo "🏢 CENTRAL ROUTES (admin.):\n";
foreach (array_slice($centralRoutes, 0, 5) as $route) {
    echo "   ✅ {$route}\n";
}
if (count($centralRoutes) > 5) {
    echo "   ... and " . (count($centralRoutes) - 5) . " more\n";
}
echo "\n";

echo "🏗️  TENANT ROUTES (tenant.):\n";
if (empty($tenantRoutes)) {
    echo "   ❌ NO TENANT ROUTES FOUND!\n";
    echo "   🔍 This is the root cause of the issue!\n\n";
} else {
    foreach ($tenantRoutes as $route) {
        echo "   ✅ {$route}\n";
    }
    echo "\n";
}

// 2. Check middleware registration
echo "🛡️  STEP 2: Middleware Analysis\n";
echo "================================================\n";

$kernel = app(\Illuminate\Contracts\Http\Kernel::class);
$reflection = new ReflectionClass($kernel);

// Check if ensure.tenant.user middleware exists
$middlewareGroups = config('middleware.groups', []);
$routeMiddleware = config('middleware.alias', []);

echo "📋 Middleware Groups:\n";
foreach ($middlewareGroups as $group => $middlewares) {
    echo "   {$group}: " . count($middlewares) . " middlewares\n";
}

echo "\n🔧 Route Middleware Aliases:\n";
$tenantMiddlewares = array_filter($routeMiddleware, function($key) {
    return str_contains($key, 'tenant') || str_contains($key, 'tenancy');
}, ARRAY_FILTER_USE_KEY);

if (empty($tenantMiddlewares)) {
    echo "   ❌ No tenant-specific middleware aliases found!\n";
} else {
    foreach ($tenantMiddlewares as $alias => $class) {
        echo "   ✅ {$alias} => {$class}\n";
    }
}

// Check if ensure.tenant.user is registered
if (isset($routeMiddleware['ensure.tenant.user'])) {
    echo "   ✅ ensure.tenant.user middleware is registered\n";
} else {
    echo "   ❌ ensure.tenant.user middleware is NOT registered!\n";
    echo "   🔍 This could be causing route loading issues!\n";
}

echo "\n";

// 3. Test tenant domain resolution
echo "🌐 STEP 3: Domain Resolution Test\n";
echo "================================================\n";

$testDomain = 'techstart.localhost';
echo "Testing domain: {$testDomain}\n";

// Check if domain exists in database
$domain = App\Models\Domain::where('domain', $testDomain)->first();
if ($domain) {
    echo "✅ Domain found in database\n";
    echo "   Tenant: {$domain->tenant->getAttribute('name')}\n";
    echo "   Tenant ID: {$domain->tenant->getKey()}\n";
    echo "   Tenant Active: " . ($domain->tenant->getAttribute('active') ? 'YES' : 'NO') . "\n";
} else {
    echo "❌ Domain NOT found in database!\n";
    echo "Available domains:\n";
    $domains = App\Models\Domain::all();
    foreach ($domains as $d) {
        echo "   - {$d->domain} -> {$d->tenant->getAttribute('name')}\n";
    }
}

echo "\n";

// 4. Test tenancy initialization
echo "🔧 STEP 4: Tenancy Initialization Test\n";
echo "================================================\n";

if ($domain) {
    try {
        // Simulate the domain-based initialization
        tenancy()->initialize($domain->tenant);
        
        echo "✅ Tenancy initialized successfully\n";
        echo "   Current tenant ID: " . tenant('id') . "\n";
        echo "   Current tenant name: " . tenant('name') . "\n";
        echo "   Tenancy initialized: " . (tenancy()->initialized ? 'YES' : 'NO') . "\n";
        
        tenancy()->end();
    } catch (Exception $e) {
        echo "❌ Tenancy initialization failed: {$e->getMessage()}\n";
    }
}

echo "\n";

// 5. Test service provider loading
echo "📦 STEP 5: Service Provider Analysis\n";
echo "================================================\n";

$providers = config('app.providers', []);
echo "Registered Service Providers:\n";

$tenancyProviders = array_filter($providers, function($provider) {
    return str_contains($provider, 'Tenanc') || str_contains($provider, 'tenant');
});

foreach ($tenancyProviders as $provider) {
    echo "   ✅ {$provider}\n";
}

// Check bootstrap/providers.php
$bootstrapProviders = require base_path('bootstrap/providers.php');
echo "\nBootstrap Providers:\n";
foreach ($bootstrapProviders as $provider) {
    $shortName = class_basename($provider);
    echo "   ✅ {$shortName} ({$provider})\n";
}

echo "\n";

// 6. Check if TenancyServiceProvider is properly loaded
echo "🔍 STEP 6: TenancyServiceProvider Investigation\n";
echo "================================================\n";

try {
    $tenancyProvider = app()->getProvider(\App\Providers\TenancyServiceProvider::class);
    if ($tenancyProvider) {
        echo "✅ TenancyServiceProvider is registered\n";
        
        // Check if it has the mapTenantRoutes method
        $reflection = new ReflectionClass($tenancyProvider);
        if ($reflection->hasMethod('mapTenantRoutes')) {
            echo "✅ mapTenantRoutes method exists\n";
        } else {
            echo "❌ mapTenantRoutes method NOT found!\n";
        }
        
        if ($reflection->hasMethod('boot')) {
            echo "✅ boot method exists\n";
        } else {
            echo "❌ boot method NOT found!\n";
        }
    } else {
        echo "❌ TenancyServiceProvider is NOT registered!\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking TenancyServiceProvider: {$e->getMessage()}\n";
}

echo "\n";

// 7. Test route file accessibility
echo "📁 STEP 7: Route File Analysis\n";
echo "================================================\n";

$tenantRoutesPath = base_path('routes/tenant.php');
if (file_exists($tenantRoutesPath)) {
    echo "✅ routes/tenant.php exists\n";
    echo "   File size: " . filesize($tenantRoutesPath) . " bytes\n";
    echo "   Readable: " . (is_readable($tenantRoutesPath) ? 'YES' : 'NO') . "\n";
    
    // Check syntax
    $syntaxCheck = shell_exec("php -l {$tenantRoutesPath} 2>&1");
    if (str_contains($syntaxCheck, 'No syntax errors')) {
        echo "✅ Syntax check passed\n";
    } else {
        echo "❌ Syntax error found:\n{$syntaxCheck}\n";
    }
} else {
    echo "❌ routes/tenant.php does NOT exist!\n";
}

echo "\n=== DIAGNOSIS SUMMARY ===\n";

if (empty($tenantRoutes)) {
    echo "🚨 ROOT CAUSE IDENTIFIED: TENANT ROUTES NOT LOADING\n";
    echo "\nPossible causes:\n";
    echo "1. TenancyServiceProvider->mapTenantRoutes() not being called\n";
    echo "2. Middleware 'ensure.tenant.user' not registered\n";
    echo "3. Route loading happening before tenant initialization\n";
    echo "4. Service provider boot order issue\n";
    
    echo "\n🔧 RECOMMENDED FIXES:\n";
    echo "1. Check middleware registration in bootstrap/app.php\n";
    echo "2. Verify TenancyServiceProvider boot method execution\n";
    echo "3. Test direct route loading without middleware\n";
} else {
    echo "✅ Tenant routes are loaded - issue might be elsewhere\n";
}
