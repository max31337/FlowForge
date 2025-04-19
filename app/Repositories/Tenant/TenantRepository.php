<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant;
use App\Repositories\Tenant\TenantRepositoryInterface;
use Illuminate\Support\Collection;

class TenantRepository implements TenantRepositoryInterface
{
    public function all(): Collection
    {
        return Tenant::all();
    }

    public function find(int $id): ?Tenant
    {
        return Tenant::find($id);
    }

    public function findBySlug(string $slug): ?Tenant
    {
        return Tenant::where('slug', $slug)->first();
    }

    public function create(array $data): Tenant
    {
        return Tenant::create($data);
    }

    public function update(Tenant $tenant, array $data): bool
    {
        return $tenant->update($data);
    }

    public function delete(Tenant $tenant): bool
    {
        return $tenant->delete();
    }
}