<?php

namespace App\Repositories;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Repositories\Contracts\TenantRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TenantRepository implements TenantRepositoryInterface
{
    public function findByDomain(string $domain): ?Tenant
    {
        return Tenant::where('domain', $domain)->first();
    }

    public function findBySlug(string $slug): ?Tenant
    {
        return Tenant::where('slug', $slug)->first();
    }

    public function getTenantWithRelations(int $tenantId): ?Tenant
    {
        return Tenant::with([
            'users',
            'projects.tasks',
            'settings',
            'subscription'
        ])->find($tenantId);
    }

    public function getUsers(int $tenantId): Collection
    {
        return User::where('tenant_id', $tenantId)
                   ->whereNull('deleted_at')
                   ->get();
    }

    public function getActiveProjects(int $tenantId): Collection
    {
        return Project::where('tenant_id', $tenantId)
                      ->where('archived', false)
                      ->get();
    }

    public function getTaskSummary(int $tenantId): array
    {
        $tasks = Task::where('tenant_id', $tenantId)->get();

        return [
            'total'      => $tasks->count(),
            'completed'  => $tasks->where('status', 'completed')->count(),
            'in_progress'=> $tasks->where('status', 'in_progress')->count(),
            'backlog'    => $tasks->where('status', 'backlog')->count(),
        ];
    }

    public function createTenantWithDefaults(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            $tenant = Tenant::create($data);

            // Set default settings
            $tenant->settings()->create([
                'theme' => 'light',
                'timezone' => config('app.timezone'),
            ]);

            // Set default subscription (e.g., trial)
            $tenant->subscription()->create([
                'plan' => 'trial',
                'expires_at' => now()->addDays(14),
            ]);

            return $tenant;
        });
    }
}