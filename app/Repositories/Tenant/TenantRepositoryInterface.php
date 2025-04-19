<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant;
use Illuminate\Support\Collection;

interface TenantRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Tenant;
    public function findBySlug(string $slug): ?Tenant;
    public function create(array $data): Tenant;
    public function update(Tenant $tenant, array $data): bool;
    public function delete(Tenant $tenant): bool;
}