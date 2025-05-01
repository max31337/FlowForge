<?php

namespace App\Repositories\Contracts;

use App\Models\Tenant;
use Illuminate\Support\Collection;

interface TenantRepositoryInterface
{
    public function findByDomain(string $domain): ?Tenant;
    public function findBySlug(string $slug): ?Tenant;
    public function getTenantWithRelations(int $tenantId): ?Tenant;
    public function getUsers(int $tenantId): Collection;
    public function getActiveProjects(int $tenantId): Collection;
    public function getTaskSummary(int $tenantId): array;
    public function createTenantWithDefaults(array $data): Tenant;
}