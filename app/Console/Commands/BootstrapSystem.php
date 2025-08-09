<?php

namespace App\Console\Commands;

use Database\Seeders\SystemBootstrapSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BootstrapSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:bootstrap {--force : Force bootstrap even if data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bootstrap FlowForge system with essential data (superadmin, system tenant, permissions)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 FlowForge System Bootstrap');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Check if system is already bootstrapped
        if (!$this->option('force') && $this->isSystemBootstrapped()) {
            $this->warn('⚠️  System appears to be already bootstrapped.');
            $this->info('   Use --force flag to run anyway.');
            $this->newLine();
            $this->displaySystemStatus();
            return 0;
        }

        $this->newLine();
        $this->info('Bootstrapping system with essential data...');

        try {
            // Run the bootstrap seeder
            Artisan::call('db:seed', [
                '--class' => SystemBootstrapSeeder::class,
                '--force' => true
            ]);

            $this->info('✅ System bootstrap completed successfully!');
            $this->newLine();
            $this->displaySystemStatus();

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Bootstrap failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check if the system is already bootstrapped.
     */
    private function isSystemBootstrapped(): bool
    {
        try {
            // Check if superadmin exists
            $superadminExists = \App\Models\User::where('email', 'admin@flowforge.com')->exists();
            
            // Check if system tenant exists
            $systemTenantExists = \App\Models\Tenant::where('slug', 'flowforge-platform')->exists();
            
            // Check if central admin role exists
            $centralAdminRoleExists = \App\Models\Role::where('name', 'central_admin')
                ->whereNull('tenant_id')
                ->exists();

            return $superadminExists && $systemTenantExists && $centralAdminRoleExists;
        } catch (\Exception $e) {
            // If we can't check (e.g., tables don't exist), assume not bootstrapped
            return false;
        }
    }

    /**
     * Display current system status.
     */
    private function displaySystemStatus(): void
    {
        $this->warn('📊 SYSTEM STATUS:');

        try {
            // Check superadmin
            $superadmin = \App\Models\User::where('email', 'admin@flowforge.com')->first();
            if ($superadmin) {
                $this->info("   ✅ Superadmin: {$superadmin->name} ({$superadmin->email})");
            } else {
                $this->error('   ❌ Superadmin: Not found');
            }

            // Check system tenant
            $systemTenant = \App\Models\Tenant::where('slug', 'flowforge-platform')->first();
            if ($systemTenant) {
                $this->info("   ✅ System Tenant: {$systemTenant->name}");
            } else {
                $this->error('   ❌ System Tenant: Not found');
            }

            // Check permissions count
            $permissionsCount = \App\Models\Permission::count();
            $this->info("   ✅ Permissions: {$permissionsCount} permissions loaded");

            // Check central admin role
            $centralAdminRole = \App\Models\Role::where('name', 'central_admin')
                ->whereNull('tenant_id')
                ->first();
            if ($centralAdminRole) {
                $this->info("   ✅ Central Admin Role: {$centralAdminRole->display_name}");
            } else {
                $this->error('   ❌ Central Admin Role: Not found');
            }

            $this->newLine();
            $this->warn('🔗 QUICK ACCESS:');
            $this->info('   Admin URL: http://localhost:8000/admin');
            $this->info('   Email: admin@flowforge.com');
            $this->info('   Password: superadmin@flowforge123!');

        } catch (\Exception $e) {
            $this->error('   ❌ Could not check system status: ' . $e->getMessage());
        }
    }
}
