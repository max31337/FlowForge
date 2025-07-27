<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Tenant Dashboard Debug ===\n\n";

// Check domains first
echo "🌐 Checking domains:\n";
$domains = App\Models\Domain::all();
foreach ($domains as $domain) {
    echo "   {$domain->domain} -> Tenant: {$domain->tenant->getAttribute('name')}\n";
}
echo "\n";

// Test tenant
$tenant = App\Models\Tenant::where('slug', 'techstart')->first();
if (!$tenant) {
    echo "❌ No techstart tenant found\n";
    exit(1);
}

echo "🏢 Tenant: {$tenant->name}\n";
echo "   ID: {$tenant->getKey()}\n";

// Initialize tenancy
tenancy()->initialize($tenant);
echo "✅ Tenancy initialized: " . (tenancy()->initialized ? 'YES' : 'NO') . "\n";

// Test user
$user = App\Models\User::where('tenant_id', $tenant->getKey())
    ->where('email', 'owner@techstart.com')
    ->first();

if ($user) {
    echo "👤 User: {$user->name} ({$user->email})\n";
    echo "   Role: " . ($user->role ? $user->role->name : 'NO ROLE') . "\n";
    
    // Simulate authentication
    Auth::login($user);
    echo "✅ User authenticated\n";
    
    // Test Livewire components
    echo "\n🧪 Testing Livewire Components:\n";
    
    try {
        $recentProjects = new App\Livewire\Tenant\RecentProjects();
        $projects = $recentProjects->projects();
        echo "   RecentProjects: " . $projects->count() . " projects found\n";
        
        $dashboardStats = new App\Livewire\Tenant\DashboardStats();
        echo "   DashboardStats: Component created successfully\n";
        
        $quickActions = new App\Livewire\Tenant\QuickActions();
        echo "   QuickActions: Component created successfully\n";
        
        echo "✅ All Livewire components working\n";
        
    } catch (Exception $e) {
        echo "❌ Livewire component error: " . $e->getMessage() . "\n";
    }
    
    Auth::logout();
} else {
    echo "❌ No owner user found for techstart tenant\n";
}

tenancy()->end();
