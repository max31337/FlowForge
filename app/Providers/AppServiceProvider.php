<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Eloquent\TenantRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\TaskRepository;
use App\Repositories\Eloquent\ProjectRepository;

use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;

use App\Services\Tenant\TenantServiceInterface;
use App\Services\Tenant\TenantService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);

        // Services
        $this->app->bind(TenantServiceInterface::class, TenantService::class);


    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
