<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Tenant Domain Routing Debug ===\n\n";

// Check if techstart tenant exists and has domain
$tenant = App\Models\Tenant::where('slug', 'techstart')->first();
if (!$tenant) {
    echo "❌ Techstart tenant not found\n";
    exit(1);
}

echo "🏢 Found tenant: {$tenant->name}\n";
echo "   ID: {$tenant->getKey()}\n";

// Check domains
$domains = $tenant->domains;
echo "   Domains: " . $domains->pluck('domain')->join(', ') . "\n\n";

// Try to simulate the domain-based tenant resolution
$testDomain = 'techstart.localhost';
echo "🔍 Testing domain: {$testDomain}\n";

// Find tenant by domain
$foundTenant = App\Models\Tenant::whereHas('domains', function($query) use ($testDomain) {
    $query->where('domain', $testDomain);
})->first();

if ($foundTenant) {
    echo "✅ Tenant found for domain: {$foundTenant->name}\n";
    
    // Try to initialize tenancy
    try {
        tenancy()->initialize($foundTenant);
        echo "✅ Tenancy initialized successfully\n";
        echo "   Tenant ID: " . tenant('id') . "\n";
        echo "   Tenant Name: " . tenant('name') . "\n";
        
        tenancy()->end();
    } catch (Exception $e) {
        echo "❌ Failed to initialize tenancy: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ No tenant found for domain: {$testDomain}\n";
    echo "   Available domains:\n";
    $allDomains = App\Models\Domain::all();
    foreach ($allDomains as $domain) {
        echo "   - {$domain->domain} (tenant: {$domain->tenant->name})\n";
    }
}

// Check if route exists when we simulate tenant context
echo "\n🛣️  Route Testing:\n";
$_SERVER['HTTP_HOST'] = $testDomain;
$_SERVER['REQUEST_METHOD'] = 'GET';

try {
    // Check if routes are loaded
    $router = app('router');
    $routes = $router->getRoutes();
    
    $tenantRoutes = [];
    foreach ($routes as $route) {
        if (str_contains($route->getName() ?? '', 'tenant.')) {
            $tenantRoutes[] = $route->getName() . ' => ' . $route->uri();
        }
    }
    
    echo "   Found tenant routes:\n";
    foreach ($tenantRoutes as $routeInfo) {
        echo "   - {$routeInfo}\n";
    }
    
    if (empty($tenantRoutes)) {
        echo "   ❌ No tenant routes found!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error checking routes: " . $e->getMessage() . "\n";
}
