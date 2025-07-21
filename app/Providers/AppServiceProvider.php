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
    }
}
