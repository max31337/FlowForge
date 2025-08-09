<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AppBootstrapSeeder extends Seeder
{
    /**
     * Bootstrap the application with essential system data.
     * This seeder is designed to run automatically and safely on every deployment.
     */
    public function run(): void
    {
        $this->createSystemPermissions();
        $this->createCentralAdminRole();
        $this->createSystemTenant();
        $this->createSuperAdmin();
    }

    /**
     * Create system permissions if they don't exist.
     */
    private function createSystemPermissions(): void
    {
        $systemPermissions = [
            [
                'name' => 'manage_tenants',
                'display_name' => 'Manage Tenants',
                'description' => 'Manage tenant organizations',
                'resource' => 'tenants',
                'action' => 'manage',
                'is_system' => true
            ],
            [
                'name' => 'impersonate_users',
                'display_name' => 'Impersonate Users',
                'description' => 'Login as other users',
                'resource' => 'users',
                'action' => 'impersonate',
                'is_system' => true
            ],
        ];

        foreach ($systemPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }

    /**
     * Create central admin role if it doesn't exist.
     */
    private function createCentralAdminRole(): Role
    {
        $centralAdminRole = Role::firstOrCreate(
            ['name' => 'central_admin', 'tenant_id' => null],
            [
                'display_name' => 'Central Administrator',
                'description' => 'Super admin with access to all tenants',
                'is_system' => true,
            ]
        );

        // Ensure central admin has all permissions
        $allPermissions = Permission::pluck('name')->toArray();
        $centralAdminRole->syncPermissions($allPermissions);

        return $centralAdminRole;
    }

    /**
     * Create system tenant if it doesn't exist.
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

        return $systemTenant;
    }

    /**
     * Create superadmin user if it doesn't exist.
     */
    private function createSuperAdmin(): User
    {
        $centralAdminRole = Role::where('name', 'central_admin')
            ->whereNull('tenant_id')
            ->first();

        if (!$centralAdminRole) {
            throw new \Exception('Central admin role not found. Please run permissions seeder first.');
        }

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

        return $superAdmin;
    }
}
