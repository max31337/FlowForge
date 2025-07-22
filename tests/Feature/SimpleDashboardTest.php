<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class SimpleDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_component_loads_without_tenant()
    {
        Livewire::test('tenant.dashboard-stats')
            ->assertSet('stats.total_projects', 0)
            ->assertSet('stats.total_tasks', 0)
            ->assertSet('stats.pending_tasks', 0)
            ->assertSet('stats.in_progress_tasks', 0)
            ->assertSet('stats.completed_tasks', 0)
            ->assertSet('stats.total_users', 0)
            ->assertSet('stats.completion_rate', 0);
    }

    public function test_dashboard_stats_refresh_works()
    {
        Livewire::test('tenant.dashboard-stats')
            ->call('refresh')
            ->assertDispatched('stats-refreshed');
    }

    public function test_recent_projects_component_loads()
    {
        Livewire::test('tenant.recent-projects')
            ->assertSee('Recent Projects');
    }

    public function test_recent_tasks_component_loads()
    {
        Livewire::test('tenant.recent-tasks')
            ->assertSee('Recent Tasks');
    }

    public function test_quick_actions_component_loads()
    {
        // Create a simple user for authentication context
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $this->actingAs($user);
        
        Livewire::test('tenant.quick-actions')
            ->assertSee('Quick Actions');
    }
}
