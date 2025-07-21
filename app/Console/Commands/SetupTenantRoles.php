<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Console\Command;

class SetupTenantRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:setup-roles {tenant_slug?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up default roles for tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantSlug = $this->argument('tenant_slug');
        $seeder = new RolePermissionSeeder();
        $seeder->setCommand($this); // Add command reference to seeder

        try {
            if ($tenantSlug) {
                $tenant = Tenant::where('slug', $tenantSlug)->first();
                if (!$tenant) {
                    $this->error("Tenant '{$tenantSlug}' not found!");
                    return 1;
                }
                
                $seeder->createTenantRoles($tenant->id);
                $this->info("✅ Roles created for tenant: {$tenant->name}");
            } else {
                $tenants = Tenant::all();
                $this->info("Found {$tenants->count()} tenants");
                
                foreach ($tenants as $tenant) {
                    $this->info("Creating roles for: {$tenant->name} ({$tenant->slug})");
                    $seeder->createTenantRoles($tenant->id);
                    $this->info("✅ Roles created for tenant: {$tenant->name}");
                }
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
