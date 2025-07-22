<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActualTenantAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_user_can_access_their_dashboard()
    {
        // Seed the data first
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\SystemBootstrapSeeder']);
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\DummyDataSeeder']);
        
        // Get techstart tenant and user
        $tenant = Tenant::where('slug', 'techstart')->first();
        $user = User::where('email', 'owner@techstart.com')->first();
        
        $this->assertNotNull($tenant);
        $this->assertNotNull($user);
        
        echo "\nFound tenant: {$tenant->name} (ID: {$tenant->id})";
        echo "\nFound user: {$user->name} ({$user->email})";
        
        // Authenticate as the user
        $this->actingAs($user);
        
        // Simulate accessing techstart.localhost domain
        $response = $this->withHeaders([
            'HTTP_HOST' => 'techstart.localhost'
        ])->get('/dashboard');
        
        echo "\nResponse status: " . $response->getStatusCode();
        
        if ($response->getStatusCode() === 200) {
            echo "\nDashboard accessed successfully! ✓";
            // Check if we can see tenant-specific content
            $content = $response->getContent();
            if (str_contains($content, 'TechStart Solutions') || str_contains($content, 'techstart')) {
                echo "\nTenant-specific content found! ✓";
            } else {
                echo "\nWarning: No tenant-specific content found";
            }
        } else {
            echo "\nFailed to access dashboard: " . $response->getStatusCode();
        }
        
        $response->assertStatus(200);
    }
}
