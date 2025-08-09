<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * SystemBootstrapSeeder
 * 
 * This seeder is designed to run safely on every deployment/startup.
 * It creates essential system data that the application needs to function.
 * All operations are idempotent (safe to run multiple times).
 */
class SystemBootstrapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Bootstrap: Initializing FlowForge system...');

        // Step 1: Create system permissions
        $this->createSystemPermissions();

        // Step 2: Create central admin role
        $this->createCentralAdminRole();

        // Step 3: Create system tenant
        $systemTenant = $this->createSystemTenant();

        // Step 4: Create superadmin user
        $superAdmin = $this->createSuperAdmin();

        $this->command->info('✅ System bootstrap completed successfully!');
        
        // Show important info
        $this->displaySystemInfo($superAdmin, $systemTenant);
    }

    /**
     * Create essential system permissions.
     */
    private function createSystemPermissions(): void
    {
        $systemPermissions = [
            // Central admin permissions
            [
                'name' => 'manage_tenants',
                'display_name' => 'Manage Tenants',
                'description' => 'Create, update, delete tenant organizations',
                'resource' => 'tenants',
                'action' => 'manage',
                'is_system' => true
            ],
            [
                'name' => 'impersonate_users',
                'display_name' => 'Impersonate Users',
                'description' => 'Login as other users for support purposes',
                'resource' => 'users',
                'action' => 'impersonate',
                'is_system' => true
            ],
            [
                'name' => 'view_system_analytics',
                'display_name' => 'View System Analytics',
                'description' => 'Access system-wide analytics and reports',
                'resource' => 'analytics',
                'action' => 'view',
                'is_system' => true
            ],
            [
                'name' => 'manage_system_settings',
                'display_name' => 'Manage System Settings',
                'description' => 'Configure system-wide settings',
                'resource' => 'settings',
                'action' => 'manage',
                'is_system' => true
            ],

            // Tenant-level permissions
            [
                'name' => 'manage_users',
                'display_name' => 'Manage Users',
                'description' => 'Manage team members and their roles',
                'resource' => 'users',
                'action' => 'manage',
                'is_system' => false
            ],
            [
                'name' => 'create_users',
                'display_name' => 'Create Users',
                'description' => 'Invite new team members',
                'resource' => 'users',
                'action' => 'create',
                'is_system' => false
            ],
            [
                'name' => 'read_users',
                'display_name' => 'View Users',
                'description' => 'View team member profiles',
                'resource' => 'users',
                'action' => 'read',
                'is_system' => false
            ],
            [
                'name' => 'update_users',
                'display_name' => 'Update Users',
                'description' => 'Edit team member information',
                'resource' => 'users',
                'action' => 'update',
                'is_system' => false
            ],
            [
                'name' => 'delete_users',
                'display_name' => 'Delete Users',
                'description' => 'Remove team members',
                'resource' => 'users',
                'action' => 'delete',
                'is_system' => false
            ],

            // Project permissions
            [
                'name' => 'manage_projects',
                'display_name' => 'Manage Projects',
                'description' => 'Full project management access',
                'resource' => 'projects',
                'action' => 'manage',
                'is_system' => false
            ],
            [
                'name' => 'create_projects',
                'display_name' => 'Create Projects',
                'description' => 'Create new projects',
                'resource' => 'projects',
                'action' => 'create',
                'is_system' => false
            ],
            [
                'name' => 'read_projects',
                'display_name' => 'View Projects',
                'description' => 'View project details',
                'resource' => 'projects',
                'action' => 'read',
                'is_system' => false
            ],
            [
                'name' => 'update_projects',
                'display_name' => 'Update Projects',
                'description' => 'Edit project information',
                'resource' => 'projects',
                'action' => 'update',
                'is_system' => false
            ],
            [
                'name' => 'delete_projects',
                'display_name' => 'Delete Projects',
                'description' => 'Remove projects',
                'resource' => 'projects',
                'action' => 'delete',
                'is_system' => false
            ],

            // Task permissions
            [
                'name' => 'manage_tasks',
                'display_name' => 'Manage Tasks',
                'description' => 'Full task management access',
                'resource' => 'tasks',
                'action' => 'manage',
                'is_system' => false
            ],
            [
                'name' => 'create_tasks',
                'display_name' => 'Create Tasks',
                'description' => 'Create new tasks',
                'resource' => 'tasks',
                'action' => 'create',
                'is_system' => false
            ],
            [
                'name' => 'read_tasks',
                'display_name' => 'View Tasks',
                'description' => 'View task details',
                'resource' => 'tasks',
                'action' => 'read',
                'is_system' => false
            ],
            [
                'name' => 'update_tasks',
                'display_name' => 'Update Tasks',
                'description' => 'Edit task information',
                'resource' => 'tasks',
                'action' => 'update',
                'is_system' => false
            ],
            [
                'name' => 'delete_tasks',
                'display_name' => 'Delete Tasks',
                'description' => 'Remove tasks',
                'resource' => 'tasks',
                'action' => 'delete',
                'is_system' => false
            ],

            // Category permissions
            [
                'name' => 'manage_categories',
                'display_name' => 'Manage Categories',
                'description' => 'Manage task and project categories',
                'resource' => 'categories',
                'action' => 'manage',
                'is_system' => false
            ],
            [
                'name' => 'read_categories',
                'display_name' => 'View Categories',
                'description' => 'View category information',
                'resource' => 'categories',
                'action' => 'read',
                'is_system' => false
            ],

            // Reporting permissions
            [
                'name' => 'view_reports',
                'display_name' => 'View Reports',
                'description' => 'Access reports and analytics',
                'resource' => 'reports',
                'action' => 'view',
                'is_system' => false
            ],

            // Settings permissions
            [
                'name' => 'manage_tenant_settings',
                'display_name' => 'Manage Tenant Settings',
                'description' => 'Configure tenant settings and preferences',
                'resource' => 'settings',
                'action' => 'manage',
                'is_system' => false
            ],
        ];

        foreach ($systemPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        $this->command->info('  ✅ System permissions created/verified');
    }

    /**
     * Create central admin role.
     */
    private function createCentralAdminRole(): Role
    {
        $centralAdminRole = Role::firstOrCreate(
            ['name' => 'central_admin', 'tenant_id' => null],
            [
                'display_name' => 'Central Administrator',
                'description' => 'Super admin with access to all tenants and system management',
                'is_system' => true,
            ]
        );

        // Ensure central admin has all permissions
        $allPermissions = Permission::pluck('name')->toArray();
        $centralAdminRole->syncPermissions($allPermissions);

        $this->command->info('  ✅ Central admin role created/verified');

        return $centralAdminRole;
    }

    /**
     * Create system tenant.
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
                    'description' => 'System tenant for FlowForge platform administration',
                    'created_by' => 'system_bootstrap'
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
            $this->command->info("  ✅ System tenant 'FlowForge Platform' created");
        } else {
            $this->command->info("  ℹ️  System tenant 'FlowForge Platform' already exists");
        }

        return $systemTenant;
    }

    /**
     * Create superadmin user.
     */
    private function createSuperAdmin(): User
    {
        // Get central admin role
        $centralAdminRole = Role::where('name', 'central_admin')
            ->whereNull('tenant_id')
            ->first();

        if (!$centralAdminRole) {
            throw new \Exception('Central admin role not found. Please run this seeder again.');
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

        if ($superAdmin->wasRecentlyCreated) {
            $this->command->info("  ✅ Superadmin 'Mark Anthony Navarro' created");
        } else {
            $this->command->info("  ℹ️  Superadmin 'Mark Anthony Navarro' already exists");
        }

        return $superAdmin;
    }

    /**
     * Display important system information.
     */
    private function displaySystemInfo(User $superAdmin, Tenant $systemTenant): void
    {
        $this->command->newLine();
        $this->command->warn('🔐 SUPERADMIN ACCESS:');
        $this->command->info("   Name: {$superAdmin->name}");
        $this->command->info("   Email: {$superAdmin->email}");
        $this->command->info('   Password: superadmin@flowforge123!');
        $this->command->info('   URL: http://localhost:8000/admin');
        $this->command->newLine();
        $this->command->warn('🏢 SYSTEM TENANT:');
        $this->command->info("   Name: {$systemTenant->name}");
        $this->command->info("   Slug: {$systemTenant->slug}");
        $this->command->info("   Domain: " . ($systemTenant->domains->first()->domain ?? 'N/A'));
        $this->command->newLine();
    }
}
