<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Observers\CategoryObserver;
use App\Observers\ProjectObserver;
use App\Observers\TaskObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers for auto-filling tenant_id
        User::observe(UserObserver::class);
        Project::observe(ProjectObserver::class);
        Task::observe(TaskObserver::class);
        Category::observe(CategoryObserver::class);

        // Register authorization gates for permissions
        $this->registerGates();
    }

    /**
     * Register authorization gates based on permissions.
     */
    protected function registerGates(): void
    {
        // Define gates for each permission
        $permissions = [
            'manage_users',
            'manage_projects', 
            'manage_tasks',
            'manage_categories',
            'view_reports',
            'manage_tenant_settings',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function (User $user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }

        // Additional role-based gates
        Gate::define('is-owner', function (User $user) {
            return $user->isOwner();
        });

        Gate::define('is-admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('is-central-admin', function (User $user) {
            return $user->isCentralAdmin();
        });
    }
}
