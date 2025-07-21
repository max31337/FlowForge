<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create 
                            {name : The tenant name}
                            {slug? : The tenant slug (optional)}
                            {--email= : The tenant email}
                            {--domain= : The tenant domain (defaults to slug.localhost)}
                            {--plan=free : The tenant plan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant with domain';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $slug = $this->argument('slug') ?: Str::slug($name);
        $email = $this->option('email');
        $domain = $this->option('domain') ?: $slug . '.localhost';
        $plan = $this->option('plan');

        // Validate slug is unique
        if (Tenant::where('slug', $slug)->exists()) {
            $this->error("Tenant with slug '{$slug}' already exists!");
            return 1;
        }

        $this->info("Creating tenant: {$name}");

        // Create tenant
        $tenant = Tenant::create([
            'name' => $name,
            'slug' => $slug,
            'email' => $email,
            'plan' => $plan,
            'active' => true,
        ]);

        $this->info("✅ Tenant created with ID: {$tenant->id}");

        // Create domain
        $tenantDomain = $tenant->domains()->create([
            'domain' => $domain,
        ]);

        $this->info("✅ Domain created: {$domain}");

        // Display tenant information
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $tenant->id],
                ['Name', $tenant->name],
                ['Slug', $tenant->slug],
                ['Email', $tenant->email ?? 'N/A'],
                ['Plan', $tenant->plan],
                ['Domain', $domain],
                ['Active', $tenant->active ? 'Yes' : 'No'],
            ]
        );

        $this->info("🚀 Tenant successfully created and ready to use!");
        $this->comment("You can access it at: http://{$domain}:8000");

        return 0;
    }
}
