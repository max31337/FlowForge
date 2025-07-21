<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperadmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-superadmin 
                            {email : The superadmin email}
                            {name? : The superadmin name (default: Super Admin)}
                            {--password= : The password (default: password)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a superadmin user for the central admin area';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name') ?: 'Super Admin';
        $password = $this->option('password') ?: 'password';

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->error("User with email '{$email}' already exists!");
            
            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $existingUser->id],
                    ['Name', $existingUser->name],
                    ['Email', $existingUser->email],
                    ['Tenant ID', $existingUser->tenant_id ?? 'NULL (Central Admin)'],
                    ['Created', $existingUser->created_at->format('Y-m-d H:i:s')],
                ]
            );
            
            if ($existingUser->tenant_id) {
                $this->warn("This user belongs to a tenant. Superadmins should not have a tenant_id.");
            }
            
            return 1;
        }

        $this->info("Creating superadmin user...");

        // Create superadmin user (no tenant_id = central admin)
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'tenant_id' => null, // No tenant = central admin
        ]);

        $this->info("✅ Superadmin user created successfully!");

        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $user->id],
                ['Name', $user->name],
                ['Email', $user->email],
                ['Password', $password],
                ['Tenant ID', 'NULL (Central Admin)'],
                ['Email Verified', 'Yes'],
            ]
        );

        $this->info("\n🔐 Admin Access Instructions:");
        $this->line("1. Visit: http://localhost:8000/login");
        $this->line("2. Login with: {$email} / {$password}");
        $this->line("3. Access admin area: http://localhost:8000/admin");
        $this->line("4. Manage tenants: http://localhost:8000/admin/tenants");

        $this->warn("\n⚠️  Important Notes:");
        $this->line("- Only access admin area from central domains (localhost, 127.0.0.1)");
        $this->line("- Admin area is blocked from tenant domains (e.g., your-org.localhost)");
        $this->line("- Change the default password in production!");

        return 0;
    }
}
