<?php

namespace App\Services\Tenant;

use App\Models\Tenant;
use App\Repositories\Tenant\TenantRepositoryInterface;
use Illuminate\Support\Collection;

class TenantService implements TenantServiceInterface
{
    public function getAll(): Collection
    {
        return Tenant::all();
    }

    public function getById(int $id): ?Tenant
    {
        return Tenant::find($id);
    }

    public function create(array $data): Tenant
    {
        return Tenant::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $tenant = Tenant::findOrFail($id);
        return $tenant->update($data);
    }

    public function delete(int $id): bool
    {
        $tenant = Tenant::findOrFail($id);
        return $tenant->delete();
    }
}