<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Category;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎭 Creating comprehensive dummy data...');

        // Create realistic dummy tenants
        $tenants = $this->createRealisticTenants();

        // For each tenant, create comprehensive data
        foreach ($tenants as $tenant) {
            $this->seedComprehensiveTenantData($tenant);
        }

        $this->command->info('✅ Comprehensive dummy data created successfully!');
        $this->displayDummyDataInfo($tenants);
    }

    /**
     * Create realistic dummy tenants with varied characteristics.
     */
    private function createRealisticTenants(): array
    {
        $tenantProfiles = [
            // Startup
            [
                'name' => 'TechStart Solutions',
                'slug' => 'techstart',
                'email' => 'hello@techstart.com',
                'plan' => 'pro',
                'domain' => 'techstart.localhost',
                'type' => 'startup',
                'industry' => 'Technology'
            ],
            // Creative Agency
            [
                'name' => 'Creative Minds Agency',
                'slug' => 'creative-minds',
                'email' => 'contact@creativeminds.com',
                'plan' => 'enterprise',
                'domain' => 'creative.localhost',
                'type' => 'agency',
                'industry' => 'Marketing'
            ],
            // Manufacturing Company
            [
                'name' => 'Global Manufacturing Inc',
                'slug' => 'global-manufacturing',
                'email' => 'info@globalmanuf.com',
                'plan' => 'enterprise',
                'domain' => 'manufacturing.localhost',
                'type' => 'enterprise',
                'industry' => 'Manufacturing'
            ],
            // Local Business
            [
                'name' => 'Local Startup Hub',
                'slug' => 'startup-hub',
                'email' => 'team@startuphub.com',
                'plan' => 'free',
                'domain' => 'startup.localhost',
                'type' => 'startup',
                'industry' => 'Education'
            ],
            // Marketing Company
            [
                'name' => 'Digital Marketing Pro',
                'slug' => 'digital-marketing',
                'email' => 'hello@digitalmarketing.com',
                'plan' => 'pro',
                'domain' => 'marketing.localhost',
                'type' => 'agency',
                'industry' => 'Marketing'
            ],
            // Healthcare
            [
                'name' => 'HealthTech Solutions',
                'slug' => 'healthtech',
                'email' => 'admin@healthtech.com',
                'plan' => 'enterprise',
                'domain' => 'healthtech.localhost',
                'type' => 'enterprise',
                'industry' => 'Healthcare'
            ],
            // Finance
            [
                'name' => 'FinanceFlow Corp',
                'slug' => 'financeflow',
                'email' => 'contact@financeflow.com',
                'plan' => 'enterprise',
                'domain' => 'financeflow.localhost',
                'type' => 'enterprise',
                'industry' => 'Finance'
            ]
        ];

        $tenants = [];
        foreach ($tenantProfiles as $profile) {
            $tenant = Tenant::firstOrCreate(
                ['slug' => $profile['slug']],
                [
                    'name' => $profile['name'],
                    'email' => $profile['email'],
                    'plan' => $profile['plan'],
                    'active' => true,
                    'trial_ends_at' => $profile['plan'] === 'free' ? now()->addDays(30) : null,
                    'data' => [
                        'industry' => $profile['industry'],
                        'type' => $profile['type'],
                        'size' => $this->getTenantSize($profile['type']),
                        'timezone' => 'UTC',
                        'created_by' => 'dummy_seeder'
                    ]
                ]
            );

            // Create domain
            $tenant->domains()->firstOrCreate([
                'domain' => $profile['domain'],
            ]);

            $tenants[] = $tenant;

            if ($tenant->wasRecentlyCreated) {
                $this->command->info("  ✅ Tenant '{$profile['name']}' created with domain {$profile['domain']}");
            }
        }

        return $tenants;
    }

    /**
     * Get appropriate team size based on tenant type.
     */
    private function getTenantSize(string $type): string
    {
        return match($type) {
            'startup' => '1-10',
            'agency' => '11-50',
            'enterprise' => '201-500',
            default => '11-50'
        };
    }

    /**
     * Seed comprehensive data for a specific tenant.
     */
    private function seedComprehensiveTenantData(Tenant $tenant): void
    {
        $this->command->info("📊 Seeding comprehensive data for: {$tenant->name}");

        // 1. Create tenant roles using existing seeder
        $this->createTenantRoles($tenant);

        // 2. Create realistic users
        $users = $this->createRealisticUsers($tenant);

        // 3. Create categories
        $categories = $this->createTenantCategories($tenant);

        // 4. Create projects with realistic data
        $projects = $this->createRealisticProjects($tenant, $users);

        // 5. Create tasks for projects
        $this->createRealisticTasks($tenant, $projects, $categories, $users);

        $this->command->info("  ✅ Comprehensive data created for {$tenant->name}");
    }

    /**
     * Create tenant roles.
     */
    private function createTenantRoles(Tenant $tenant): void
    {
        $roleSeeder = new RolePermissionSeeder();
        $roleSeeder->setCommand($this->command);
        $roleSeeder->createTenantRoles($tenant->id);
    }

    /**
     * Create realistic users with proper role distribution.
     */
    private function createRealisticUsers(Tenant $tenant): array
    {
        // Get roles for this tenant
        $roles = Role::where('tenant_id', $tenant->id)->get();
        $ownerRole = $roles->where('name', 'owner')->first();
        $adminRole = $roles->where('name', 'admin')->first();
        $managerRole = $roles->where('name', 'manager')->first();
        $userRole = $roles->where('name', 'user')->first();

        $users = [];

        // Create core team based on tenant type
        $tenantData = $tenant->data ?? [];
        $tenantType = $tenantData['type'] ?? 'startup';

        // Core leadership team
        $coreTeam = $this->getCoreTeamForType($tenantType, $tenant->slug);

        foreach ($coreTeam as $memberData) {
            $role = match($memberData['role']) {
                'owner' => $ownerRole,
                'admin' => $adminRole,
                'manager' => $managerRole,
                default => $userRole
            };

            $user = User::factory()->create([
                'name' => $memberData['name'],
                'email' => $memberData['email'],
                'tenant_id' => $tenant->id,
                'role_id' => $role->id,
                'email_verified_at' => now(),
                'password' => bcrypt('password'), // All dummy users have password: 'password'
            ]);

            $users[] = $user;
        }

        // Add additional team members based on tenant size
        $additionalUsersCount = $this->getAdditionalUsersCount($tenantType);
        
        for ($i = 0; $i < $additionalUsersCount; $i++) {
            $user = User::factory()->professional()->create([
                'tenant_id' => $tenant->id,
                'role_id' => $userRole->id,
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Get core team structure based on tenant type.
     */
    private function getCoreTeamForType(string $type, string $slug): array
    {
        $baseTeam = [
            [
                'name' => 'John Smith',
                'email' => "owner@{$slug}.com",
                'role' => 'owner'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => "admin@{$slug}.com",
                'role' => 'admin'
            ]
        ];

        // Add role-specific team members
        $additionalMembers = match($type) {
            'startup' => [
                ['name' => 'Mike Developer', 'email' => "mike@{$slug}.com", 'role' => 'user'],
                ['name' => 'Emma Designer', 'email' => "emma@{$slug}.com", 'role' => 'user'],
            ],
            'agency' => [
                ['name' => 'David Project Manager', 'email' => "manager@{$slug}.com", 'role' => 'manager'],
                ['name' => 'Lisa Creative Director', 'email' => "creative@{$slug}.com", 'role' => 'manager'],
                ['name' => 'Alex Account Manager', 'email' => "accounts@{$slug}.com", 'role' => 'user'],
            ],
            'enterprise' => [
                ['name' => 'Robert Operations Manager', 'email' => "operations@{$slug}.com", 'role' => 'manager'],
                ['name' => 'Jennifer HR Manager', 'email' => "hr@{$slug}.com", 'role' => 'manager'],
                ['name' => 'Michael IT Manager', 'email' => "it@{$slug}.com", 'role' => 'manager'],
                ['name' => 'Amanda Sales Manager', 'email' => "sales@{$slug}.com", 'role' => 'manager'],
            ],
            default => []
        };

        return array_merge($baseTeam, $additionalMembers);
    }

    /**
     * Get number of additional users to create.
     */
    private function getAdditionalUsersCount(string $type): int
    {
        return match($type) {
            'startup' => rand(2, 6),
            'agency' => rand(5, 15),
            'enterprise' => rand(15, 30),
            default => rand(3, 8)
        };
    }

    /**
     * Create tenant categories.
     */
    private function createTenantCategories(Tenant $tenant): array
    {
        $tenantData = $tenant->data ?? [];
        $industry = $tenantData['industry'] ?? 'Technology';

        // Industry-specific categories
        $categoryMap = [
            'Technology' => ['Development', 'DevOps', 'QA/Testing', 'UI/UX Design', 'Product Management', 'Infrastructure'],
            'Marketing' => ['Campaign Management', 'Content Creation', 'Social Media', 'SEO/SEM', 'Analytics', 'Brand Strategy'],
            'Manufacturing' => ['Production', 'Quality Control', 'Supply Chain', 'Maintenance', 'Safety', 'Logistics'],
            'Healthcare' => ['Patient Care', 'Compliance', 'Research', 'Administration', 'IT Systems', 'Training'],
            'Finance' => ['Accounting', 'Audit', 'Risk Management', 'Investment', 'Compliance', 'Customer Service'],
            'Education' => ['Curriculum', 'Student Services', 'Administration', 'Technology', 'Research', 'Events'],
        ];

        $categoryNames = $categoryMap[$industry] ?? $categoryMap['Technology'];

        $categories = [];
        foreach ($categoryNames as $name) {
            $category = Category::factory()->create([
                'name' => $name,
                'tenant_id' => $tenant->id,
                'description' => "Tasks and projects related to {$name}",
                'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)), // Random color
                'is_active' => true,
            ]);
            $categories[] = $category;
        }

        return $categories;
    }

    /**
     * Create realistic projects based on tenant industry.
     */
    private function createRealisticProjects(Tenant $tenant, array $users): array
    {
        $tenantData = $tenant->data ?? [];
        $industry = $tenantData['industry'] ?? 'Technology';
        $type = $tenantData['type'] ?? 'startup';

        // Industry and type-specific project templates
        $projectTemplates = $this->getProjectTemplatesForIndustry($industry, $type);

        $projects = [];

        // Create template-based projects
        foreach ($projectTemplates as $template) {
            $project = Project::factory()->create([
                'name' => $template['name'],
                'description' => $template['description'],
                'tenant_id' => $tenant->id,
                'status' => $template['status'] ?? 'active',
                'priority' => $template['priority'] ?? 'medium',
                'start_date' => now()->subDays(rand(1, 90)),
                'due_date' => $template['status'] === 'completed' ? 
                    now()->subDays(rand(1, 30)) : 
                    now()->addDays(rand(30, 180)),
                'budget' => $template['budget'] ?? null,
            ]);
            $projects[] = $project;
        }

        // Add some additional random projects
        $additionalCount = match($type) {
            'startup' => rand(2, 4),
            'agency' => rand(4, 8),
            'enterprise' => rand(6, 12),
            default => rand(3, 6)
        };

        for ($i = 0; $i < $additionalCount; $i++) {
            $project = Project::factory()->create([
                'tenant_id' => $tenant->id,
            ]);
            $projects[] = $project;
        }

        return $projects;
    }

    /**
     * Get project templates based on industry and type.
     */
    private function getProjectTemplatesForIndustry(string $industry, string $type): array
    {
        $templates = [
            'Technology' => [
                ['name' => 'Website Redesign', 'description' => 'Complete overhaul of company website with modern design', 'status' => 'active', 'priority' => 'high'],
                ['name' => 'Mobile App Development', 'description' => 'Native mobile application for iOS and Android', 'status' => 'active', 'priority' => 'high'],
                ['name' => 'API Integration', 'description' => 'Integrate third-party APIs for enhanced functionality', 'status' => 'planning', 'priority' => 'medium'],
                ['name' => 'Security Audit', 'description' => 'Comprehensive security review and improvements', 'status' => 'completed', 'priority' => 'urgent'],
            ],
            'Marketing' => [
                ['name' => 'Q1 Marketing Campaign', 'description' => 'Multi-channel marketing campaign for product launch', 'status' => 'active', 'priority' => 'high'],
                ['name' => 'Brand Identity Refresh', 'description' => 'Update brand guidelines and visual identity', 'status' => 'completed', 'priority' => 'medium'],
                ['name' => 'Content Strategy 2025', 'description' => 'Comprehensive content strategy for the year', 'status' => 'planning', 'priority' => 'medium'],
                ['name' => 'Social Media Analytics', 'description' => 'Implement advanced analytics for social media ROI', 'status' => 'active', 'priority' => 'low'],
            ],
            // Add more industry templates as needed
        ];

        return $templates[$industry] ?? $templates['Technology'];
    }

    /**
     * Create realistic tasks for projects.
     */
    private function createRealisticTasks(Tenant $tenant, array $projects, array $categories, array $users): void
    {
        foreach ($projects as $project) {
            // Create 8-20 tasks per project
            $taskCount = rand(8, 20);

            for ($i = 0; $i < $taskCount; $i++) {
                Task::factory()->create([
                    'tenant_id' => $tenant->id,
                    'project_id' => $project->id,
                    'category_id' => collect($categories)->random()->id,
                    'assigned_to' => rand(0, 1) ? collect($users)->random()->id : null,
                    'created_by' => collect($users)->random()->id, // Add the required created_by field
                    'status' => $this->getRealisticTaskStatus($project->status),
                    'priority' => collect(['low', 'medium', 'high', 'urgent'])->random(),
                    'due_date' => rand(0, 1) ? now()->addDays(rand(1, 60)) : null,
                    'estimated_hours' => rand(1, 40),
                    'actual_hours' => rand(0, 1) ? rand(1, 50) : null,
                ]);
            }
        }
    }

    /**
     * Get realistic task status based on project status.
     */
    private function getRealisticTaskStatus(string $projectStatus): string
    {
        return match($projectStatus) {
            'completed' => collect(['completed', 'completed', 'completed', 'cancelled'])->random(),
            'active' => collect(['pending', 'in_progress', 'completed', 'review'])->random(),
            'planning' => collect(['pending', 'pending'])->random(),
            'on_hold' => collect(['review', 'pending'])->random(),
            'cancelled' => collect(['cancelled', 'completed'])->random(),
            default => collect(['pending', 'in_progress', 'completed'])->random()
        };
    }

    /**
     * Display dummy data information.
     */
    private function displayDummyDataInfo(array $tenants): void
    {
        $this->command->newLine();
        $this->command->warn('🏢 DUMMY TENANT ACCESS:');
        foreach ($tenants as $tenant) {
            $domain = $tenant->domains->first()->domain ?? 'N/A';
            $this->command->info("   - {$tenant->name}: http://{$domain}");
        }
        $this->command->newLine();
        $this->command->warn('👤 DUMMY USER CREDENTIALS:');
        $this->command->info('   Username: Use emails like owner@[slug].com, admin@[slug].com');
        $this->command->info('   Password: password (for all dummy users)');
        $this->command->newLine();
        $this->command->warn('📝 EXAMPLE LOGINS:');
        $this->command->info('   - owner@techstart.com / password');
        $this->command->info('   - admin@creative-minds.com / password');
        $this->command->info('   - manager@manufacturing.com / password');
    }
}
