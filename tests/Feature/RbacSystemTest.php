<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the RBAC system
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolePermissionSeeder']);
    }

    public function test_roles_are_created_for_tenants()
    {
        $tenant = Tenant::factory()->create();
        
        // Create roles for this tenant (this would normally be done during tenant creation)
        $this->artisan('rbac:setup-tenant', ['tenant_id' => $tenant->id]);
        
        $roles = Role::where('tenant_id', $tenant->id)->get();
        
        $this->assertGreaterThan(0, $roles->count());
        $this->assertTrue($roles->pluck('name')->contains('owner'));
        $this->assertTrue($roles->pluck('name')->contains('admin'));
        $this->assertTrue($roles->pluck('name')->contains('user'));
    }
    
    public function test_permissions_are_assigned_to_roles()
    {
        $tenant = Tenant::factory()->create();
        $this->artisan('rbac:setup-tenant', ['tenant_id' => $tenant->id]);
        
        $ownerRole = Role::where('tenant_id', $tenant->id)
                         ->where('name', 'owner')
                         ->first();
        
        $this->assertNotNull($ownerRole);
        $this->assertGreaterThan(0, $ownerRole->permissions()->count());
    }
    
    public function test_user_has_permissions_through_role()
    {
        $tenant = Tenant::factory()->create();
        $this->artisan('rbac:setup-tenant', ['tenant_id' => $tenant->id]);
        
        $ownerRole = Role::where('tenant_id', $tenant->id)
                         ->where('name', 'owner')
                         ->first();
        
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role_id' => $ownerRole->id
        ]);
        
        // Test permission checking
        $permissions = $ownerRole->permissions()->pluck('name')->toArray();
        
        foreach ($permissions as $permission) {
            $this->assertTrue(
                $user->hasPermission($permission),
                "User should have permission: {$permission}"
            );
        }
    }
    
    public function test_user_cannot_access_other_tenant_permissions()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        
        $this->artisan('rbac:setup-tenant', ['tenant_id' => $tenant1->id]);
        $this->artisan('rbac:setup-tenant', ['tenant_id' => $tenant2->id]);
        
        $role1 = Role::where('tenant_id', $tenant1->id)->where('name', 'owner')->first();
        $role2 = Role::where('tenant_id', $tenant2->id)->where('name', 'owner')->first();
        
        $user1 = User::factory()->create(['tenant_id' => $tenant1->id, 'role_id' => $role1->id]);
        $user2 = User::factory()->create(['tenant_id' => $tenant2->id, 'role_id' => $role2->id]);
        
        // Users should only have access to their own tenant's roles
        $this->assertEquals($tenant1->id, $user1->role->tenant_id);
        $this->assertEquals($tenant2->id, $user2->role->tenant_id);
        $this->assertNotEquals($user1->role->tenant_id, $user2->role->tenant_id);
    }
}
