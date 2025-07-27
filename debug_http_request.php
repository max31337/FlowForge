<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

echo "=== HTTP REQUEST SIMULATION ===\n\n";

// Simulate the exact request
$request = \Illuminate\Http\Request::create(
    'http://techstart.localhost:8000/dashboard',
    'GET',
    [],
    [],
    [],
    [
        'HTTP_HOST' => 'techstart.localhost',
        'SERVER_NAME' => 'techstart.localhost',
        'SERVER_PORT' => '8000',
        'REQUEST_URI' => '/dashboard',
        'REQUEST_METHOD' => 'GET',
    ]
);

echo "🌐 Request Details:\n";
echo "   URL: " . $request->fullUrl() . "\n";
echo "   Host: " . $request->getHost() . "\n";
echo "   Path: " . $request->path() . "\n";

try {
    // Get the route for this request
    $route = app('router')->getRoutes()->match($request);
    echo "   ✅ Route matched: " . $route->getName() . "\n";
    echo "   ✅ Route URI: " . $route->uri() . "\n";
    echo "   ✅ Route Action: " . json_encode($route->getAction()) . "\n";
    
    // Check middleware
    $middleware = $route->gatherMiddleware();
    echo "   Middleware stack:\n";
    foreach ($middleware as $m) {
        echo "     - " . (is_string($m) ? $m : get_class($m)) . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Route matching failed: " . $e->getMessage() . "\n";
    echo "   This means the route is not accessible!\n";
}

// Test tenancy initialization with this request
echo "\n🏢 Tenancy Initialization Test:\n";
try {
    // Simulate the InitializeTenancyByDomain middleware
    $tenant = \App\Models\Tenant::whereHas('domains', function($query) use ($request) {
        $query->where('domain', $request->getHost());
    })->first();
    
    if ($tenant) {
        echo "   ✅ Tenant found for domain: " . $tenant->name . "\n";
        tenancy()->initialize($tenant);
        echo "   ✅ Tenancy initialized successfully\n";
    } else {
        echo "   ❌ No tenant found for domain: " . $request->getHost() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Tenancy initialization failed: " . $e->getMessage() . "\n";
}

// Test if user exists and can be authenticated
echo "\n👤 User Authentication Test:\n";
if (tenancy()->initialized) {
    $user = \App\Models\User::where('tenant_id', tenant('id'))
        ->where('email', 'owner@techstart.com')
        ->first();
    
    if ($user) {
        echo "   ✅ User found: " . $user->name . "\n";
        echo "   ✅ User email: " . $user->email . "\n";
        echo "   ✅ User tenant_id: " . $user->getAttribute('tenant_id') . "\n";
        echo "   ✅ Current tenant_id: " . tenant('id') . "\n";
        echo "   ✅ Match: " . ($user->getAttribute('tenant_id') === tenant('id') ? 'YES' : 'NO') . "\n";
    } else {
        echo "   ❌ No user found for current tenant\n";
    }
}

echo "\n🎯 Next Steps:\n";
echo "   1. If route matching failed, check route registration\n";
echo "   2. If tenancy failed, check domain configuration\n";
echo "   3. If user not found, check user data\n";
echo "   4. Try accessing via browser and check network tab\n";

tenancy()->end();
