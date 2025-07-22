<?php

namespace App\Console\Commands\Debug;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestRbac extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:test 
                            {tenant_slug : The tenant slug}
                            {--create-users : Create test users with different roles}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test RBAC system for a tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantSlug = $this->argument('tenant_slug');
        $createUsers = $this->option('create-users');

        // Find tenant
        $tenant = Tenant::where('slug', $tenantSlug)->first();
        if (!$tenant) {
            $this->error("Tenant '{$tenantSlug}' not found!");
            return 1;
        }

        $this->info("Testing RBAC for tenant: {$tenant->name}");

        // Show available roles for this tenant
        $roles = Role::where('tenant_id', $tenant->id)->get();
        $this->info("\nAvailable roles for this tenant:");
        $this->table(
            ['ID', 'Name', 'Display Name', 'Description', 'Is Default'],
            $roles->map(function ($role) {
                return [
                    $role->id,
                    $role->name,
                    $role->display_name,
                    $role->description,
                    $role->is_default ? 'Yes' : 'No',
                ];
            })
        );

        // Show permissions for each role
        foreach ($roles as $role) {
            $permissions = $role->permissions()->get(['name', 'display_name']);
            $this->info("\nPermissions for '{$role->display_name}' role:");
            if ($permissions->count() > 0) {
                $this->table(
                    ['Permission', 'Display Name'],
                    $permissions->map(fn($p) => [$p->name, $p->display_name])
                );
            } else {
                $this->warn("No permissions assigned to this role.");
            }
        }

        // Create test users if requested
        if ($createUsers) {
            $this->info("\nCreating test users with different roles...");
            
            $testUsers = [
                ['role' => 'owner', 'name' => 'Owner User', 'email' => "owner@{$tenantSlug}.com"],
                ['role' => 'admin', 'name' => 'Admin User', 'email' => "admin@{$tenantSlug}.com"],
                ['role' => 'manager', 'name' => 'Manager User', 'email' => "manager@{$tenantSlug}.com"],
                ['role' => 'user', 'name' => 'Regular User', 'email' => "user@{$tenantSlug}.com"],
            ];

            foreach ($testUsers as $userData) {
                $role = Role::where('tenant_id', $tenant->id)
                           ->where('name', $userData['role'])
                           ->first();

                if (!$role) {
                    $this->warn("Role '{$userData['role']}' not found for this tenant");
                    continue;
                }

                // Check if user already exists
                $existingUser = User::where('email', $userData['email'])->first();
                if ($existingUser) {
                    $this->warn("User {$userData['email']} already exists - updating role");
                    $existingUser->update(['role_id' => $role->id]);
                    $user = $existingUser;
                } else {
                    $user = User::create([
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'password' => Hash::make('password'),
                        'tenant_id' => $tenant->id,
                        'role_id' => $role->id,
                        'email_verified_at' => now(),
                    ]);
                }

                $this->info("✅ Created/Updated {$userData['role']}: {$userData['email']} / password");
            }
        }

        // List users for this tenant
        $users = User::where('tenant_id', $tenant->id)->with('role')->get();
        $this->info("\nUsers in this tenant:");
        if ($users->count() > 0) {
            $this->table(
                ['Name', 'Email', 'Role', 'Permissions Count'],
                $users->map(function ($user) {
                    $permissionCount = $user->role ? $user->role->permissions()->count() : 0;
                    return [
                        $user->name,
                        $user->email,
                        $user->role ? $user->role->display_name : 'No Role',
                        $permissionCount,
                    ];
                })
            );
        } else {
            $this->warn("No users found for this tenant");
        }

        // Test some permission checks
        if ($users->count() > 0) {
            $this->info("\nTesting permission checks:");
            $testUser = $users->first();
            
            $permissions = ['create_projects', 'manage_users', 'view_analytics', 'manage_tenants'];
            foreach ($permissions as $permission) {
                $hasPermission = $testUser->hasPermission($permission);
                $status = $hasPermission ? '✅' : '❌';
                $this->line("{$status} {$testUser->name} - {$permission}: " . ($hasPermission ? 'ALLOWED' : 'DENIED'));
            }
        }

        $this->info("\n🚀 RBAC system is working! Test with different users at:");
        $this->line("http://{$tenant->domains->first()->domain}:8000/login");

        return 0;
    }
}
