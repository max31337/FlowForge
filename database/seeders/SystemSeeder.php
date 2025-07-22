<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Setting up system data...');

        // Create system tenant first
        $systemTenant = $this->createSystemTenant();
        
        // Create superadmin user
        $this->createSuperAdmin();

        $this->command->info('✅ System setup completed successfully!');
    }

    /**
     * Create the system tenant.
     */
    private function createSystemTenant(): Tenant
    {
        $systemTenant = Tenant::firstOrCreate(
            ['slug' => 'flowforge-platform'],
            [
                'name' => 'FlowForge Platform',
                'email' => 'platform@flowforge.com',
                'plan' => 'enterprise',
                'active' => true,
                'data' => [
                    'is_system' => true,
                    'description' => 'System tenant for FlowForge platform administration'
                ]
            ]
        );

        // Create domain for system tenant if it doesn't exist
        if ($systemTenant->domains()->count() === 0) {
            $systemTenant->domains()->create([
                'domain' => 'system.flowforge.local',
            ]);
        }

        if ($systemTenant->wasRecentlyCreated) {
            $this->command->info("✅ System tenant 'FlowForge Platform' created");
        } else {
            $this->command->info("ℹ️  System tenant 'FlowForge Platform' already exists");
        }

        return $systemTenant;
    }

    /**
     * Create the superadmin user.
     */
    private function createSuperAdmin(): User
    {
        // Get or create central admin role
        $centralAdminRole = Role::firstOrCreate(
            ['name' => 'central_admin', 'tenant_id' => null],
            [
                'display_name' => 'Central Administrator',
                'description' => 'Super admin with access to all tenants',
                'is_system' => true,
            ]
        );

        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@flowforge.com'],
            [
                'name' => 'Mark Anthony Navarro',
                'password' => Hash::make('superadmin@flowforge123!'),
                'email_verified_at' => now(),
                'tenant_id' => null, // Central admin has no tenant
                'role_id' => $centralAdminRole->id,
            ]
        );

        if ($superAdmin->wasRecentlyCreated) {
            $this->command->info("✅ Superadmin 'Mark Anthony Navarro' created");
            $this->command->warn("📧 Email: admin@flowforge.com");
            $this->command->warn("🔑 Password: superadmin@flowforge123!");
        } else {
            $this->command->info("ℹ️  Superadmin 'Mark Anthony Navarro' already exists");
        }

        return $superAdmin;
    }
}
