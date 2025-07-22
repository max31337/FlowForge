<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class TenantDashboardTest extends TestCase
{
    use RefreshDatabase;

    private $tenant;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Bootstrap system
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\SystemBootstrapSeeder']);
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolePermissionSeeder']);
        
        // Create test tenant
        $this->tenant = Tenant::factory()->create([
            'name' => 'Test Dashboard Company',
            'slug' => 'test-dashboard'
        ]);
        
        // Create test user with proper role
        $ownerRole = Role::where('name', 'owner')->whereNull('tenant_id')->first();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role_id' => $ownerRole->id,
        ]);
    }

    public function test_dashboard_displays_correctly_with_tenant_context()
    {
        $this->actingAs($this->user);
        
        // Initialize tenancy
        tenancy()->initialize($this->tenant);
        
        $response = $this->get('/tenant/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Test Dashboard Company Dashboard');
        $response->assertSee($this->user->name);
    }

    public function test_dashboard_stats_component_shows_tenant_data()
    {
        $this->actingAs($this->user);
        tenancy()->initialize($this->tenant);
        
        // Create some test data
        $project = Project::factory()->create(['tenant_id' => $this->tenant->id]);
        Task::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
            'project_id' => $project->id,
            'status' => 'pending'
        ]);
        Task::factory()->count(2)->create([
            'tenant_id' => $this->tenant->id,
            'project_id' => $project->id,
            'status' => 'completed'
        ]);
        
        Livewire::test('tenant.dashboard-stats')
            ->assertSet('stats.total_projects', 1)
            ->assertSet('stats.total_tasks', 5)
            ->assertSet('stats.pending_tasks', 3)
            ->assertSet('stats.completed_tasks', 2)
            ->assertSet('stats.completion_rate', 40.0);
    }

    public function test_dashboard_stats_component_isolates_tenant_data()
    {
        $this->actingAs($this->user);
        tenancy()->initialize($this->tenant);
        
        // Create another tenant with data
        $otherTenant = Tenant::factory()->create(['slug' => 'other-tenant']);
        $otherProject = Project::factory()->create(['tenant_id' => $otherTenant->id]);
        Task::factory()->count(10)->create([
            'tenant_id' => $otherTenant->id,
            'project_id' => $otherProject->id
        ]);
        
        // Create data for our tenant
        $project = Project::factory()->create(['tenant_id' => $this->tenant->id]);
        Task::factory()->count(2)->create([
            'tenant_id' => $this->tenant->id,
            'project_id' => $project->id
        ]);
        
        Livewire::test('tenant.dashboard-stats')
            ->assertSet('stats.total_projects', 1)
            ->assertSet('stats.total_tasks', 2);
    }

    public function test_dashboard_quick_actions_shows_only_permitted_actions()
    {
        $this->actingAs($this->user);
        tenancy()->initialize($this->tenant);
        
        Livewire::test('tenant.quick-actions')
            ->assertSee('New Project')
            ->assertSee('New Task')
            ->assertSee('Manage Users');
    }

    public function test_recent_projects_component_shows_tenant_projects()
    {
        $this->actingAs($this->user);
        tenancy()->initialize($this->tenant);
        
        // Create projects for our tenant
        $project1 = Project::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Project 1'
        ]);
        $project2 = Project::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Project 2'
        ]);
        
        // Create project for another tenant (should not appear)
        $otherTenant = Tenant::factory()->create(['slug' => 'other']);
        Project::factory()->create([
            'tenant_id' => $otherTenant->id,
            'name' => 'Other Tenant Project'
        ]);
        
        Livewire::test('tenant.recent-projects')
            ->assertSee('Test Project 1')
            ->assertSee('Test Project 2')
            ->assertDontSee('Other Tenant Project');
    }

    public function test_recent_tasks_component_shows_tenant_tasks()
    {
        $this->actingAs($this->user);
        tenancy()->initialize($this->tenant);
        
        $project = Project::factory()->create(['tenant_id' => $this->tenant->id]);
        
        // Create tasks for our tenant
        $task1 = Task::factory()->create([
            'tenant_id' => $this->tenant->id,
            'project_id' => $project->id,
            'title' => 'Test Task 1'
        ]);
        $task2 = Task::factory()->create([
            'tenant_id' => $this->tenant->id,
            'project_id' => $project->id,
            'title' => 'Test Task 2'
        ]);
        
        // Create task for another tenant (should not appear)
        $otherTenant = Tenant::factory()->create(['slug' => 'other']);
        $otherProject = Project::factory()->create(['tenant_id' => $otherTenant->id]);
        Task::factory()->create([
            'tenant_id' => $otherTenant->id,
            'project_id' => $otherProject->id,
            'title' => 'Other Tenant Task'
        ]);
        
        Livewire::test('tenant.recent-tasks')
            ->assertSee('Test Task 1')
            ->assertSee('Test Task 2')
            ->assertDontSee('Other Tenant Task');
    }

    public function test_dashboard_handles_non_tenant_context_gracefully()
    {
        $this->actingAs($this->user);
        // Don't initialize tenancy - should handle gracefully
        
        Livewire::test('tenant.dashboard-stats')
            ->assertSet('stats.total_projects', 0)
            ->assertSet('stats.total_tasks', 0);
    }

    public function test_dashboard_refresh_functionality()
    {
        $this->actingAs($this->user);
        tenancy()->initialize($this->tenant);
        
        Livewire::test('tenant.dashboard-stats')
            ->call('refresh')
            ->assertEmitted('stats-refreshed');
    }
}
