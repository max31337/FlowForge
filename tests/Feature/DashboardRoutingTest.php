<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_redirects_to_admin_dashboard()
    {
        // Create a central admin role
        $centralAdminRole = Role::create([
            'name' => 'central_admin',
            'display_name' => 'Central Administrator',
            'description' => 'Super admin with access to all tenants',
            'is_system' => true,
            'tenant_id' => null,
        ]);

        // Create superadmin user
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@flowforge.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'tenant_id' => null,
            'role_id' => $centralAdminRole->id,
        ]);

        // Login as superadmin
        $this->actingAs($superadmin);

        // Access /dashboard on central domain
        $response = $this->get('/dashboard');

        // Should redirect to admin dashboard
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_tenant_user_redirects_to_tenant_dashboard()
    {
        // Create a tenant
        $tenant = Tenant::create([
            'name' => 'Test Company',
            'slug' => 'testcompany',
            'data' => json_encode([]),
        ]);

        // Create tenant role
        $ownerRole = Role::create([
            'name' => 'owner',
            'display_name' => 'Owner',
            'description' => 'Tenant owner',
            'tenant_id' => $tenant->id,
            'is_system' => true,
        ]);

        // Create tenant user
        $tenantUser = User::create([
            'name' => 'Tenant User',
            'email' => 'owner@testcompany.com',
            'password' => bcrypt('password'),
            'tenant_id' => $tenant->id,
            'role_id' => $ownerRole->id,
        ]);

        // Initialize tenancy
        tenancy()->initialize($tenant);

        // Login as tenant user
        $this->actingAs($tenantUser);

        // Access /dashboard on tenant domain
        $response = $this->get('/dashboard');

        // Should redirect to tenant dashboard
        $response->assertRedirect(route('tenant.dashboard'));
    }

    public function test_admin_dashboard_shows_stats()
    {
        // Create a central admin role
        $centralAdminRole = Role::create([
            'name' => 'central_admin',
            'display_name' => 'Central Administrator',
            'description' => 'Super admin with access to all tenants',
            'is_system' => true,
            'tenant_id' => null,
        ]);

        // Create superadmin user
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@flowforge.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'tenant_id' => null,
            'role_id' => $centralAdminRole->id,
        ]);

        // Login as superadmin
        $this->actingAs($superadmin);

        // Access admin dashboard directly
        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('FlowForge Admin Dashboard');
        $response->assertSee('Total Tenants');
    }
}
