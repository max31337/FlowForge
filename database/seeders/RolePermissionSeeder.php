<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    protected $command;

    public function setCommand($command)
    {
        $this->command = $command;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management
            ['name' => 'create_users', 'display_name' => 'Create Users', 'description' => 'Create new users', 'resource' => 'users', 'action' => 'create'],
            ['name' => 'read_users', 'display_name' => 'View Users', 'description' => 'View user information', 'resource' => 'users', 'action' => 'read'],
            ['name' => 'update_users', 'display_name' => 'Edit Users', 'description' => 'Edit user information', 'resource' => 'users', 'action' => 'update'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'description' => 'Delete users', 'resource' => 'users', 'action' => 'delete'],
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'description' => 'Full user management', 'resource' => 'users', 'action' => 'manage'],

            // Project management
            ['name' => 'create_projects', 'display_name' => 'Create Projects', 'description' => 'Create new projects', 'resource' => 'projects', 'action' => 'create'],
            ['name' => 'read_projects', 'display_name' => 'View Projects', 'description' => 'View project information', 'resource' => 'projects', 'action' => 'read'],
            ['name' => 'update_projects', 'display_name' => 'Edit Projects', 'description' => 'Edit project information', 'resource' => 'projects', 'action' => 'update'],
            ['name' => 'delete_projects', 'display_name' => 'Delete Projects', 'description' => 'Delete projects', 'resource' => 'projects', 'action' => 'delete'],
            ['name' => 'manage_projects', 'display_name' => 'Manage Projects', 'description' => 'Full project management', 'resource' => 'projects', 'action' => 'manage'],

            // Task management
            ['name' => 'create_tasks', 'display_name' => 'Create Tasks', 'description' => 'Create new tasks', 'resource' => 'tasks', 'action' => 'create'],
            ['name' => 'read_tasks', 'display_name' => 'View Tasks', 'description' => 'View task information', 'resource' => 'tasks', 'action' => 'read'],
            ['name' => 'update_tasks', 'display_name' => 'Edit Tasks', 'description' => 'Edit task information', 'resource' => 'tasks', 'action' => 'update'],
            ['name' => 'delete_tasks', 'display_name' => 'Delete Tasks', 'description' => 'Delete tasks', 'resource' => 'tasks', 'action' => 'delete'],
            ['name' => 'assign_tasks', 'display_name' => 'Assign Tasks', 'description' => 'Assign tasks to users', 'resource' => 'tasks', 'action' => 'assign'],

            // Category management
            ['name' => 'create_categories', 'display_name' => 'Create Categories', 'description' => 'Create new categories', 'resource' => 'categories', 'action' => 'create'],
            ['name' => 'read_categories', 'display_name' => 'View Categories', 'description' => 'View category information', 'resource' => 'categories', 'action' => 'read'],
            ['name' => 'update_categories', 'display_name' => 'Edit Categories', 'description' => 'Edit category information', 'resource' => 'categories', 'action' => 'update'],
            ['name' => 'delete_categories', 'display_name' => 'Delete Categories', 'description' => 'Delete categories', 'resource' => 'categories', 'action' => 'delete'],

            // Settings management
            ['name' => 'manage_settings', 'display_name' => 'Manage Settings', 'description' => 'Manage organization settings', 'resource' => 'settings', 'action' => 'manage'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'description' => 'Manage user roles and permissions', 'resource' => 'roles', 'action' => 'manage'],
            ['name' => 'view_analytics', 'display_name' => 'View Analytics', 'description' => 'View analytics and reports', 'resource' => 'analytics', 'action' => 'read'],

            // Central admin permissions
            ['name' => 'manage_tenants', 'display_name' => 'Manage Tenants', 'description' => 'Manage tenant organizations', 'resource' => 'tenants', 'action' => 'manage', 'is_system' => true],
            ['name' => 'impersonate_users', 'display_name' => 'Impersonate Users', 'description' => 'Login as other users', 'resource' => 'users', 'action' => 'impersonate', 'is_system' => true],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']], 
                $permission
            );
        }

        // Create central admin role (no tenant_id)
        $centralAdminRole = Role::firstOrCreate(
            ['name' => 'central_admin', 'tenant_id' => null],
            [
                'display_name' => 'Central Administrator',
                'description' => 'Super admin with access to all tenants',
                'is_system' => true,
            ]
        );

        // Give central admin all permissions
        $centralAdminRole->syncPermissions(Permission::pluck('name')->toArray());

        $this->command->info('✅ RBAC permissions and central admin role created');
    }

    /**
     * Create default tenant roles with permissions.
     */
    public function createTenantRoles(string $tenantId): void
    {
        // Owner role - full access within tenant
        $ownerRole = Role::firstOrCreate(
            ['name' => 'owner', 'tenant_id' => $tenantId],
            [
                'display_name' => 'Owner',
                'description' => 'Tenant owner with full permissions',
                'is_system' => true,
            ]
        );

        $ownerPermissions = [
            'manage_users', 'create_users', 'read_users', 'update_users', 'delete_users',
            'manage_projects', 'create_projects', 'read_projects', 'update_projects', 'delete_projects',
            'create_tasks', 'read_tasks', 'update_tasks', 'delete_tasks', 'assign_tasks',
            'create_categories', 'read_categories', 'update_categories', 'delete_categories',
            'manage_settings', 'manage_roles', 'view_analytics'
        ];
        $ownerRole->syncPermissions($ownerPermissions);

        // Admin role - most permissions except user management
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'tenant_id' => $tenantId],
            [
                'display_name' => 'Administrator',
                'description' => 'Administrator with most permissions',
                'is_system' => true,
            ]
        );

        $adminPermissions = [
            'read_users', 'create_users', 'update_users',
            'manage_projects', 'create_projects', 'read_projects', 'update_projects', 'delete_projects',
            'create_tasks', 'read_tasks', 'update_tasks', 'delete_tasks', 'assign_tasks',
            'create_categories', 'read_categories', 'update_categories', 'delete_categories',
            'view_analytics'
        ];
        $adminRole->syncPermissions($adminPermissions);

        // Manager role - project and task management
        $managerRole = Role::firstOrCreate(
            ['name' => 'manager', 'tenant_id' => $tenantId],
            [
                'display_name' => 'Manager',
                'description' => 'Project and task manager',
                'is_system' => true,
            ]
        );

        $managerPermissions = [
            'read_users',
            'create_projects', 'read_projects', 'update_projects',
            'create_tasks', 'read_tasks', 'update_tasks', 'assign_tasks',
            'read_categories',
            'view_analytics'
        ];
        $managerRole->syncPermissions($managerPermissions);

        // User role - basic access
        $userRole = Role::firstOrCreate(
            ['name' => 'user', 'tenant_id' => $tenantId],
            [
                'display_name' => 'User',
                'description' => 'Basic user with limited permissions',
                'is_default' => true,
                'is_system' => true,
            ]
        );

        $userPermissions = [
            'read_users',
            'read_projects',
            'read_tasks', 'update_tasks', // Can only update tasks assigned to them
            'read_categories'
        ];
        $userRole->syncPermissions($userPermissions);

        if ($this->command) {
            $this->command->info("✅ Default roles created for tenant: {$tenantId}");
        }
    }
}
