<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestTenantAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:test-auth 
                            {tenant_slug : The tenant slug to test with}
                            {--create-user : Create a test user for this tenant}
                            {--user-email= : Email for the test user (default: test@tenant.com)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test tenant authentication system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantSlug = $this->argument('tenant_slug');
        $createUser = $this->option('create-user');
        $userEmail = $this->option('user-email');

        // Find tenant
        $tenant = Tenant::where('slug', $tenantSlug)->first();
        if (!$tenant) {
            $this->error("Tenant with slug '{$tenantSlug}' not found!");
            return 1;
        }

        $this->info("Testing tenant authentication for: {$tenant->name}");
        $this->line("Tenant ID: {$tenant->id}");
        $this->line("Tenant Domain: " . $tenant->domains->pluck('domain')->join(', '));

        // Create test user if requested
        if ($createUser) {
            $email = $userEmail ?: "test@{$tenantSlug}.com";
            
            // Check if user already exists
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $this->warn("User with email '{$email}' already exists!");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['ID', $existingUser->id],
                        ['Name', $existingUser->name],
                        ['Email', $existingUser->email],
                        ['Tenant ID', $existingUser->tenant_id ?? 'NULL'],
                        ['Current Tenant', $existingUser->tenant_id === $tenant->id ? 'YES' : 'NO'],
                    ]
                );
            } else {
                $user = User::create([
                    'name' => 'Test User',
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'tenant_id' => $tenant->id,
                    'email_verified_at' => now(),
                ]);

                $this->info("✅ Test user created:");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['ID', $user->id],
                        ['Name', $user->name],
                        ['Email', $user->email],
                        ['Password', 'password'],
                        ['Tenant ID', $user->tenant_id],
                    ]
                );
            }
        }

        // List all users for this tenant
        $tenantUsers = User::where('tenant_id', $tenant->id)->get();
        
        if ($tenantUsers->count() > 0) {
            $this->info("\nUsers belonging to this tenant:");
            $this->table(
                ['ID', 'Name', 'Email', 'Provider', 'Created'],
                $tenantUsers->map(function ($user) {
                    return [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->provider ?? 'email',
                        $user->created_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray()
            );
        } else {
            $this->warn("No users found for this tenant.");
        }

        // Test tenant context
        $this->info("\nTesting tenant context...");
        tenancy()->initialize($tenant);
        
        if (tenancy()->initialized) {
            $this->info("✅ Tenant context initialized successfully");
            $this->line("Current tenant ID: " . tenant('id'));
            $this->line("Current tenant name: " . tenant('name'));
        } else {
            $this->error("❌ Failed to initialize tenant context");
        }
        
        tenancy()->end();

        $this->info("\n🔐 Authentication Test Instructions:");
        $this->line("1. Visit: http://{$tenant->domains->first()->domain}:8000/login");
        $this->line("2. Try logging in with a user from this tenant");
        $this->line("3. Try logging in with a user from a different tenant");
        $this->line("4. Verify that only tenant users can access the dashboard");

        return 0;
    }
}
