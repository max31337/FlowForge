<?php

namespace App\Providers;

use Database\Seeders\AppBootstrapSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;

class SystemBootstrapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only run in non-testing environments and when database is available
        if (!app()->environment('testing') && $this->databaseExists()) {
            $this->ensureSystemData();
        }
    }

    /**
     * Ensure essential system data exists.
     */
    private function ensureSystemData(): void
    {
        try {
            // Check if migrations have been run
            if (!$this->migrationsExist()) {
                return;
            }

            // Run role permission seeder first (creates basic permissions)
            $roleSeeder = new RolePermissionSeeder();
            $roleSeeder->setCommand(new class {
                public function info($message) { /* Silent */ }
                public function warn($message) { /* Silent */ }
            });
            $roleSeeder->run();

            // Run bootstrap seeder (creates superadmin and system tenant)
            $bootstrapSeeder = new AppBootstrapSeeder();
            $bootstrapSeeder->run();

        } catch (\Exception $e) {
            // Silently fail if there are issues (e.g., during migration)
            logger()->warning('SystemBootstrap: ' . $e->getMessage());
        }
    }

    /**
     * Check if the database connection exists and is working.
     */
    private function databaseExists(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if the necessary migrations have been run.
     */
    private function migrationsExist(): bool
    {
        try {
            return \Schema::hasTable('users') && 
                   \Schema::hasTable('tenants') && 
                   \Schema::hasTable('roles') && 
                   \Schema::hasTable('permissions');
        } catch (\Exception $e) {
            return false;
        }
    }
}
