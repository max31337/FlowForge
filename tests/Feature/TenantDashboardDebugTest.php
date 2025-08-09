<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantDashboardDebugTest extends TestCase
{
    use RefreshDatabase;

    private function createMockCommand()
    {
        return new class {
            public function info($message) { echo "$message\n"; }
            public function warn($message) { echo "$message\n"; }
            public function newLine() { echo "\n"; }
            public function confirm($question, $default = false) { return true; }
        };
    }

    public function test_dashboard_data_is_properly_loaded()
    {
        // Bootstrap the system first using direct instantiation without commands
        $systemBootstrap = new \Database\Seeders\SystemBootstrapSeeder();
        $systemBootstrap->run();
        
        $roleSeeder = new \Database\Seeders\RolePermissionSeeder();
        $roleSeeder->run();
        
        $dummySeeder = new \Database\Seeders\DummyDataSeeder();
        $dummySeeder->run();

        // Get the techstart tenant
        $tenant = Tenant::where('slug', 'techstart')->first();
        $this->assertNotNull($tenant, 'Techstart tenant should exist');

        // Get an owner user from this tenant
        $ownerRole = Role::where('name', 'owner')->whereNull('tenant_id')->first();
        $user = User::where('tenant_id', $tenant->id)
            ->where('role_id', $ownerRole->id)
            ->first();
        
        $this->assertNotNull($user, 'Owner user should exist for techstart tenant');

        // Check if there's data in the tenant
        $projectCount = Project::where('tenant_id', $tenant->id)->count();
        $taskCount = Task::where('tenant_id', $tenant->id)->count();
        $userCount = User::where('tenant_id', $tenant->id)->count();

        echo "\n=== TENANT DEBUG INFO ===\n";
        echo "Tenant: {$tenant->name} (ID: {$tenant->id})\n";
        echo "Projects: {$projectCount}\n";
        echo "Tasks: {$taskCount}\n";
        echo "Users: {$userCount}\n";
        echo "Test User: {$user->name} ({$user->email})\n";
        echo "=========================\n";

        $this->assertGreaterThan(0, $projectCount, 'Should have projects');
        $this->assertGreaterThan(0, $taskCount, 'Should have tasks');
        $this->assertGreaterThan(0, $userCount, 'Should have users');

        // Test accessing the dashboard with tenant context
        $this->actingAs($user);
        
        // Initialize tenancy manually
        tenancy()->initialize($tenant);
        
        // Test the tenant dashboard controller directly
        $controller = new \App\Http\Controllers\Tenant\DashboardController();
        $response = $controller->index();
        
        echo "\nTenant dashboard view rendered successfully: ✓\n";
        
        // Also test via HTTP with tenant domain simulation
        $this->withHeaders([
            'HTTP_HOST' => 'techstart.localhost'
        ])->get('/dashboard')
        ->assertStatus(200);
    }
}
