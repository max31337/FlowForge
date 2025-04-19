
<?php

namespace App\Services\Tenant;

use App\Models\Tenant;
use Illuminate\Support\Collection;

interface TenantServiceInterface
{
    public function getAll(): Collection;

    public function getById(int $id): ?Tenant;

    public function create(array $data): Tenant;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}