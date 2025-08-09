<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class AssignUserToTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:assign-user 
                            {email : User email}
                            {tenant_slug : Tenant slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign an existing user to a tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $tenantSlug = $this->argument('tenant_slug');

        // Find user
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email '{$email}' not found!");
            return 1;
        }

        // Find tenant
        $tenant = Tenant::where('slug', $tenantSlug)->first();
        if (!$tenant) {
            $this->error("Tenant with slug '{$tenantSlug}' not found!");
            return 1;
        }

        // Check if user already belongs to a tenant
        if ($user->tenant_id) {
            $currentTenant = Tenant::find($user->tenant_id);
            $currentTenantName = $currentTenant ? $currentTenant->name : 'Unknown';
            
            if (!$this->confirm("User already belongs to tenant '{$currentTenantName}'. Do you want to reassign?")) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Assign user to tenant
        $user->update(['tenant_id' => $tenant->id]);

        $this->info("✅ User '{$email}' has been assigned to tenant '{$tenant->name}' ({$tenant->slug})");
        
        $this->table(
            ['Field', 'Value'],
            [
                ['User ID', $user->id],
                ['User Email', $user->email],
                ['User Name', $user->name],
                ['Tenant ID', $tenant->id],
                ['Tenant Name', $tenant->name],
                ['Tenant Slug', $tenant->slug],
            ]
        );

        return 0;
    }
}
