<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

echo "=== COMPREHENSIVE TENANT DASHBOARD DEBUG ===\n\n";

// 1. Check if tenant routes are registered
echo "🔍 STEP 1: Route Registration Check\n";
$allRoutes = Route::getRoutes();
$tenantRoutes = [];
$dashboardRoutes = [];

foreach ($allRoutes as $route) {
    $name = $route->getName();
    $uri = $route->uri();
    
    if (str_contains($name, 'tenant') || str_contains($uri, 'tenant')) {
        $tenantRoutes[] = ['name' => $name, 'uri' => $uri];
    }
    
    if (str_contains($name, 'dashboard') || str_contains($uri, 'dashboard')) {
        $dashboardRoutes[] = ['name' => $name, 'uri' => $uri];
    }
}

echo "   Dashboard routes found:\n";
foreach ($dashboardRoutes as $route) {
    echo "     - {$route['name']}: {$route['uri']}\n";
}

echo "   Tenant routes found:\n";
foreach ($tenantRoutes as $route) {
    echo "     - {$route['name']}: {$route['uri']}\n";
}

// 2. Test tenant context manually
echo "\n🏢 STEP 2: Tenant Context Test\n";
$tenant = App\Models\Tenant::where('slug', 'techstart')->first();
if ($tenant) {
    echo "   ✅ Tenant found: {$tenant->name}\n";
    
    // Initialize tenancy
    tenancy()->initialize($tenant);
    echo "   ✅ Tenancy initialized: " . (tenancy()->initialized ? 'YES' : 'NO') . "\n";
    echo "   ✅ Current tenant ID: " . tenant('id') . "\n";
    
    // Test user authentication
    $user = App\Models\User::where('tenant_id', $tenant->getKey())
        ->where('email', 'owner@techstart.com')
        ->first();
    
    if ($user) {
        echo "   ✅ User found: {$user->name}\n";
        Auth::login($user);
        echo "   ✅ User authenticated\n";
    } else {
        echo "   ❌ No user found for tenant\n";
        exit(1);
    }
} else {
    echo "   ❌ No techstart tenant found\n";
    exit(1);
}

// 3. Test Livewire components individually
echo "\n⚡ STEP 3: Livewire Components Test\n";

try {
    // Test DashboardStats
    echo "   Testing DashboardStats...\n";
    $dashboardStats = new App\Livewire\Tenant\DashboardStats();
    $stats = $dashboardStats->stats();
    echo "     ✅ DashboardStats: " . json_encode($stats) . "\n";
    
    // Test RecentProjects
    echo "   Testing RecentProjects...\n";
    $recentProjects = new App\Livewire\Tenant\RecentProjects();
    $projects = $recentProjects->projects();
    echo "     ✅ RecentProjects: " . $projects->count() . " projects found\n";
    
    // Test RecentTasks
    echo "   Testing RecentTasks...\n";
    $recentTasks = new App\Livewire\Tenant\RecentTasks();
    echo "     ✅ RecentTasks: Component created successfully\n";
    
    // Test QuickActions
    echo "   Testing QuickActions...\n";
    $quickActions = new App\Livewire\Tenant\QuickActions();
    echo "     ✅ QuickActions: Component created successfully\n";
    
} catch (Exception $e) {
    echo "   ❌ Livewire component error: " . $e->getMessage() . "\n";
    echo "   Stack: " . $e->getTraceAsString() . "\n";
}

// 4. Test middleware
echo "\n🛡️ STEP 4: Middleware Test\n";
$middlewares = [
    'InitializeTenancyByDomain',
    'PreventAccessFromCentralDomains',
    'EnsureTenantUser'
];

foreach ($middlewares as $middleware) {
    try {
        $class = "App\\Http\\Middleware\\{$middleware}";
        if (!class_exists($class)) {
            $class = "Stancl\\Tenancy\\Middleware\\{$middleware}";
        }
        
        if (class_exists($class)) {
            echo "   ✅ {$middleware}: Found\n";
        } else {
            echo "   ❌ {$middleware}: Not found\n";
        }
    } catch (Exception $e) {
        echo "   ❌ {$middleware}: Error - " . $e->getMessage() . "\n";
    }
}

// 5. Test view file existence
echo "\n📄 STEP 5: View Files Test\n";
$viewFiles = [
    'tenant.dashboard',
    'livewire.tenant.dashboard-stats',
    'livewire.tenant.recent-projects',
    'livewire.tenant.recent-tasks',
    'livewire.tenant.quick-actions'
];

foreach ($viewFiles as $view) {
    $viewPath = resource_path('views/' . str_replace('.', '/', $view) . '.blade.php');
    if (file_exists($viewPath)) {
        echo "   ✅ {$view}: Found\n";
    } else {
        echo "   ❌ {$view}: NOT FOUND at {$viewPath}\n";
    }
}

// 6. Test JavaScript/CSS assets
echo "\n💻 STEP 6: Asset Files Test\n";
$assetFiles = [
    'public/build/manifest.json',
    'resources/js/app.js',
    'resources/css/app.css'
];

foreach ($assetFiles as $asset) {
    $assetPath = base_path($asset);
    if (file_exists($assetPath)) {
        echo "   ✅ {$asset}: Found\n";
    } else {
        echo "   ❌ {$asset}: NOT FOUND\n";
    }
}

// 7. Test Laravel components
echo "\n🧩 STEP 7: Laravel Components Test\n";
$components = [
    'app.blade.php',
    'navigation.blade.php'
];

foreach ($components as $component) {
    $componentPath = resource_path('views/layouts/' . $component);
    if (file_exists($componentPath)) {
        echo "   ✅ layouts/{$component}: Found\n";
        
        // Check for Livewire scripts/styles
        $content = file_get_contents($componentPath);
        if (str_contains($content, '@livewireStyles')) {
            echo "     ✅ @livewireStyles found\n";
        } else {
            echo "     ❌ @livewireStyles NOT found\n";
        }
        
        if (str_contains($content, '@livewireScripts')) {
            echo "     ✅ @livewireScripts found\n";
        } else {
            echo "     ❌ @livewireScripts NOT found\n";
        }
    } else {
        echo "   ❌ layouts/{$component}: NOT FOUND\n";
    }
}

// Clean up
Auth::logout();
tenancy()->end();

echo "\n🎯 SUMMARY:\n";
echo "   - Routes: " . count($dashboardRoutes) . " dashboard routes, " . count($tenantRoutes) . " tenant routes\n";
echo "   - Tenancy: " . (tenancy()->initialized ? 'Working' : 'Not working') . "\n";
echo "   - Components: Check individual results above\n";
echo "\n   If routes are missing, the issue is route registration.\n";
echo "   If components fail, the issue is Livewire setup.\n";
echo "   If views are missing, the issue is file paths.\n";
echo "   If assets are missing, run 'npm run build'.\n";
