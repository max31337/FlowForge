<?php 
namespace App\Repositories\Project;

use App\Models\Project;
use Illuminate\Support\Collection;

interface ProjectRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Project;

    public function create(array $data): Project;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}