<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_tenant_exists()
    {
        // Run the system bootstrap seeder
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\SystemBootstrapSeeder']);
        
        $systemTenant = Tenant::where('slug', 'system')->first();
        
        $this->assertNotNull($systemTenant);
        $this->assertEquals('FlowForge Platform', $systemTenant->name);
        $this->assertTrue($systemTenant->is_active);
    }
    
    public function test_superadmin_user_exists()
    {
        // Run the system bootstrap seeder
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\SystemBootstrapSeeder']);
        
        $superadmin = User::where('email', 'admin@flowforge.com')->first();
        
        $this->assertNotNull($superadmin);
        $this->assertEquals('Mark Anthony Navarro', $superadmin->name);
        $this->assertTrue($superadmin->is_superadmin);
    }
    
    public function test_tenant_initialization()
    {
        // Create a test tenant
        $tenant = Tenant::factory()->create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant'
        ]);
        
        // Test tenant context initialization
        tenancy()->initialize($tenant);
        
        $this->assertTrue(tenancy()->initialized);
        $this->assertEquals($tenant->id, tenant('id'));
        $this->assertEquals($tenant->name, tenant('name'));
        
        tenancy()->end();
        $this->assertFalse(tenancy()->initialized);
    }
    
    public function test_tenant_user_isolation()
    {
        // Create two tenants
        $tenant1 = Tenant::factory()->create(['slug' => 'tenant1']);
        $tenant2 = Tenant::factory()->create(['slug' => 'tenant2']);
        
        // Create users for each tenant
        $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
        $user2 = User::factory()->create(['tenant_id' => $tenant2->id]);
        
        // Test that users belong to correct tenants
        $this->assertEquals($tenant1->id, $user1->tenant_id);
        $this->assertEquals($tenant2->id, $user2->tenant_id);
        
        // Test tenant user queries
        $tenant1Users = User::where('tenant_id', $tenant1->id)->get();
        $tenant2Users = User::where('tenant_id', $tenant2->id)->get();
        
        $this->assertCount(1, $tenant1Users);
        $this->assertCount(1, $tenant2Users);
        $this->assertTrue($tenant1Users->contains($user1));
        $this->assertTrue($tenant2Users->contains($user2));
    }
}
