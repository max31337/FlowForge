<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Tenant;
use App\Models\Project;
use App\Models\Category;
use App\Models\User;
use App\Models\Task;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Task Form Dropdown Data Debug ===\n\n";

// Check if we can get tenant from current domain or use a test domain
$testHost = 'techstart.localhost';
echo "Testing with host: $testHost\n";

$tenant = Tenant::whereHas('domains', function($query) use ($testHost) {
    $query->where('domain', $testHost);
})->first();

if (!$tenant) {
    echo "❌ No tenant found for host: $testHost\n";
    echo "Available tenants:\n";
    $tenants = Tenant::with('domains')->get();
    foreach ($tenants as $t) {
        echo "- Tenant {$t->id}: " . $t->domains->pluck('domain')->implode(', ') . "\n";
    }
    exit(1);
}

echo "✅ Found tenant: {$tenant->id}\n\n";

// Initialize tenancy context manually
tenancy()->initialize($tenant);

echo "=== Projects for Tenant {$tenant->id} ===\n";
$projects = Project::where('tenant_id', $tenant->id)->get();
echo "Found " . $projects->count() . " projects:\n";
foreach ($projects as $project) {
    echo "- ID: {$project->id}, Name: {$project->name}, Status: {$project->status}\n";
}

echo "\n=== Categories for Tenant {$tenant->id} ===\n";
$categories = Category::where('tenant_id', $tenant->id)->get();
echo "Found " . $categories->count() . " categories:\n";
foreach ($categories as $category) {
    echo "- ID: {$category->id}, Name: {$category->name}\n";
}

echo "\n=== Users for Tenant {$tenant->id} ===\n";
$users = User::where('tenant_id', $tenant->id)->get();
echo "Found " . $users->count() . " users:\n";
foreach ($users as $user) {
    echo "- ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
}

echo "\n=== Tasks for Tenant {$tenant->id} ===\n";
$tasks = Task::where('tenant_id', $tenant->id)->get();
echo "Found " . $tasks->count() . " tasks:\n";
foreach ($tasks as $task) {
    echo "- ID: {$task->id}, Title: {$task->title}, Status: {$task->status}\n";
}

echo "\n=== Test Task Creation ===\n";
try {
    $taskData = [
        'title' => 'Test Task ' . now()->format('H:i:s'),
        'description' => 'Test task created by debug script',
        'status' => 'pending',
        'priority' => 'medium',
        'tenant_id' => $tenant->id,
        'created_by' => $users->first()?->id ?: 1,
    ];
    
    echo "Attempting to create task with data:\n";
    print_r($taskData);
    
    $task = Task::create($taskData);
    echo "✅ Task created successfully with ID: {$task->id}\n";
    
} catch (Exception $e) {
    echo "❌ Failed to create task: " . $e->getMessage() . "\n";
}
