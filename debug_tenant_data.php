<?php
// Quick debug script to check tenant data
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FlowForge Tenant Data Check ===\n\n";

// Check techstart tenant
$tenant = App\Models\Tenant::where('slug', 'techstart')->first();
if ($tenant) {
    echo "🏢 Tenant: {$tenant->name} (ID: {$tenant->id})\n";
    echo "   Slug: {$tenant->slug}\n";
    echo "   Active: " . ($tenant->active ? 'Yes' : 'No') . "\n";
    
    $projects = App\Models\Project::where('tenant_id', $tenant->id)->count();
    $tasks = App\Models\Task::where('tenant_id', $tenant->id)->count();
    $users = App\Models\User::where('tenant_id', $tenant->id)->count();
    
    echo "   📊 Stats:\n";
    echo "      Projects: {$projects}\n";
    echo "      Tasks: {$tasks}\n";
    echo "      Users: {$users}\n\n";
    
    // Show some users
    $tenantUsers = App\Models\User::where('tenant_id', $tenant->id)->limit(3)->get();
    echo "   👥 Sample Users:\n";
    foreach ($tenantUsers as $user) {
        $roleName = $user->role ? $user->role->name : 'No Role';
        echo "      - {$user->name} ({$user->email}) - {$roleName}\n";
    }
} else {
    echo "❌ Tenant 'techstart' not found\n";
}

echo "\n=== All Tenants ===\n";
$allTenants = App\Models\Tenant::all();
foreach ($allTenants as $t) {
    $projectCount = App\Models\Project::where('tenant_id', $t->id)->count();
    $userCount = App\Models\User::where('tenant_id', $t->id)->count();
    echo "🏢 {$t->name} ({$t->slug}) - {$projectCount} projects, {$userCount} users\n";
}
