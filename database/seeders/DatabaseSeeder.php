<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting FlowForge database seeding...');
        $this->command->newLine();
        
        // 1. Bootstrap essential system data (always runs, safe to repeat)
        $this->command->warn('STEP 1: System Bootstrap');
        $this->call(SystemBootstrapSeeder::class);
        
        $this->command->newLine();
        
        // 2. Create base permissions and roles for tenants
        $this->command->warn('STEP 2: RBAC Setup');
        $this->call(RolePermissionSeeder::class);
        
        $this->command->newLine();
        
        // 3. Ask if user wants comprehensive dummy data
        $this->command->warn('STEP 3: Dummy Data (Optional)');
        if ($this->command->confirm('Do you want to create comprehensive dummy tenants and data for development?', true)) {
            $this->call(DummyDataSeeder::class);
        } else {
            $this->command->info('Skipping dummy data creation.');
        }

        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->displayQuickAccessInfo();
    }

    /**
     * Display quick access information.
     */
    private function displayQuickAccessInfo(): void
    {
        $this->command->newLine();
        $this->command->warn('📋 QUICK ACCESS INFORMATION');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();
        
        $this->command->warn('🔐 SUPERADMIN ACCESS:');
        $this->command->info('   URL: http://localhost:8000/admin');
        $this->command->info('   Email: admin@flowforge.com');
        $this->command->info('   Password: superadmin@flowforge123!');
        $this->command->info('   Name: Mark Anthony Navarro');
        $this->command->newLine();
        
        $this->command->warn('🏢 SYSTEM TENANT:');
        $this->command->info('   Name: FlowForge Platform');
        $this->command->info('   Slug: flowforge-platform');
        $this->command->info('   Domain: system.flowforge.local');
        $this->command->newLine();
        
        $this->command->warn('🎭 DUMMY TENANT EXAMPLES (if created):');
        $this->command->info('   Technology: http://techstart.localhost:8000');
        $this->command->info('   Creative: http://creative.localhost:8000');
        $this->command->info('   Manufacturing: http://manufacturing.localhost:8000');
        $this->command->info('   Healthcare: http://healthtech.localhost:8000');
        $this->command->info('   Finance: http://financeflow.localhost:8000');
        $this->command->newLine();
        
        $this->command->warn('👤 DUMMY USER ACCESS (if created):');
        $this->command->info('   Owner: owner@[tenant-slug].com / password');
        $this->command->info('   Admin: admin@[tenant-slug].com / password');
        $this->command->info('   Manager: manager@[tenant-slug].com / password');
        $this->command->info('   Example: owner@techstart.com / password');
        $this->command->newLine();
        
        $this->command->warn('🚀 NEXT STEPS:');
        $this->command->info('   1. Start server: php artisan serve');
        $this->command->info('   2. Access central admin at: http://localhost:8000/admin');
        $this->command->info('   3. Login with superadmin credentials above');
        $this->command->info('   4. Create or manage tenants from admin dashboard');
        $this->command->info('   5. Access tenant domains to test multi-tenancy');
        $this->command->newLine();
    }
}
