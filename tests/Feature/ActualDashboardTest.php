<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActualDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_works_with_manually_created_data()
    {
        // Create permissions manually
        $permissions = [
            'read_projects' => Permission::create([
                'name' => 'read_projects',
                'display_name' => 'View Projects',
                'description' => 'View project information',
                'resource' => 'projects',
                'action' => 'read'
            ]),
            'read_tasks' => Permission::create([
                'name' => 'read_tasks',
                'display_name' => 'View Tasks',
                'description' => 'View task information',
                'resource' => 'tasks',
                'action' => 'read'
            ]),
            'read_users' => Permission::create([
                'name' => 'read_users',
                'display_name' => 'View Users',
                'description' => 'View user information',
                'resource' => 'users',
                'action' => 'read'
            ]),
        ];

        // Create a tenant
        $tenant = Tenant::create([
            'name' => 'Test Company',
            'slug' => 'testcompany',
            'data' => json_encode([]),
        ]);

        // Create owner role for this tenant
        $ownerRole = Role::create([
            'name' => 'owner',
            'display_name' => 'Owner',
            'description' => 'Tenant owner with full permissions',
            'tenant_id' => $tenant->id,
            'is_system' => true,
        ]);

        // Assign permissions to the role
        $ownerRole->permissions()->attach(array_map(fn($p) => $p->id, $permissions));

        // Create a user for this tenant (before initializing tenancy)
        $user = User::create([
            'name' => 'Test Owner',
            'email' => 'owner@testcompany.com',
            'password' => bcrypt('password'),
            'tenant_id' => $tenant->id,
            'role_id' => $ownerRole->getKey(),
        ]);

        // Initialize tenancy context before creating tenant-scoped models
        tenancy()->initialize($tenant);

        // Create some projects (tenant_id will be auto-set by BelongsToTenant trait)
        $project1 = Project::create([
            'name' => 'Test Project 1',
            'description' => 'A test project',
            'status' => 'active',
            'created_by' => $user->getKey(),
        ]);

        $project2 = Project::create([
            'name' => 'Test Project 2',
            'description' => 'Another test project',
            'status' => 'active',
            'created_by' => $user->getKey(),
        ]);

        // Create some tasks (tenant_id will be auto-set by BelongsToTenant trait)
        Task::create([
            'title' => 'Test Task 1',
            'description' => 'A test task',
            'status' => 'pending',
            'priority' => 'medium',
            'project_id' => $project1->getKey(),
            'created_by' => $user->getKey(),
        ]);

        Task::create([
            'title' => 'Test Task 2',
            'description' => 'Another test task',
            'status' => 'in_progress',
            'priority' => 'high',
            'project_id' => $project2->getKey(),
            'created_by' => $user->getKey(),
        ]);

        // Act as the user (tenancy is already initialized)
        $this->actingAs($user);

        // Test the dashboard
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee($tenant->name);
        $response->assertSee('Test Project 1');
        $response->assertSee('Test Task 1');

        echo "\n=== TEST SUCCESS ===\n";
        echo "Tenant: {$tenant->name}\n";
        echo "User: {$user->name} ({$user->email})\n";
        echo "Projects: " . Project::where('tenant_id', $tenant->id)->count() . "\n";
        echo "Tasks: " . Task::where('tenant_id', $tenant->id)->count() . "\n";
        echo "Dashboard accessible: ✓\n";
        echo "===================\n";
    }
}
