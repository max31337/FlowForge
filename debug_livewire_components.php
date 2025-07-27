<?php
// Quick debug script to test Livewire component tenant context initialization
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FlowForge Livewire Component Test ===\n\n";

// Get a test tenant
$tenant = App\Models\Tenant::where('slug', 'techstart')->first();
if (!$tenant) {
    echo "❌ No techstart tenant found. Run dummy data seeder first.\n";
    exit(1);
}

echo "🏢 Testing with tenant: {$tenant->name} (ID: {$tenant->id})\n";

// Initialize tenancy
tenancy()->initialize($tenant);
echo "✅ Tenant context initialized\n";

try {
    // Test TenantAwareComponent functionality by creating a mock request
    $_SERVER['HTTP_HOST'] = 'techstart.localhost';
    $_SERVER['REQUEST_URI'] = '/dashboard';
    
    // Create an instance of one of our components
    $component = new App\Livewire\Tenant\Projects\ProjectList();
    
    // Test the getTenantId method
    $reflection = new ReflectionClass($component);
    $method = $reflection->getMethod('getTenantId');
    $method->setAccessible(true);
    $tenantId = $method->invoke($component);
    
    echo "✅ ProjectList getTenantId(): {$tenantId}\n";
    
    // Test CreateTaskForm
    $taskComponent = new App\Livewire\Tenant\Tasks\CreateTaskForm();
    $taskTenantId = $method->invoke($taskComponent);
    
    echo "✅ CreateTaskForm getTenantId(): {$taskTenantId}\n";
    
    // Test validation rules using reflection
    $taskReflection = new ReflectionClass($taskComponent);
    $rulesMethod = $taskReflection->getMethod('rules');
    $rulesMethod->setAccessible(true);
    $rules = $rulesMethod->invoke($taskComponent);
    echo "✅ CreateTaskForm validation rules loaded: " . count($rules) . " rules\n";
    
    // Test computed properties
    $projects = $taskComponent->projects();
    echo "✅ CreateTaskForm projects(): " . $projects->count() . " projects\n";
    
    echo "\n🎉 All Livewire component tests passed!\n";
    
} catch (Exception $e) {
    echo "❌ Error testing components: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

tenancy()->end();
