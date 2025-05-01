<?php

namespace App\Repositories\Activity Log;

use App\Models\ActivityLog;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Support\Collection;

class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    public function getAll(): Collection
    {
        return ActivityLog::with('user')->latest()->get();
    }

    public function getByUser(int $userId, int $limit = 50): Collection
    {
        return ActivityLog::where('user_id', $userId)
                          ->with('user')
                          ->latest()
                          ->take($limit)
                          ->get();
    }

    public function getByTenant(int $tenantId, int $limit = 50): Collection
    {
        return ActivityLog::whereHas('user', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->with('user')
        ->latest()
        ->take($limit)
        ->get();
    }

    public function create(array $data): void
    {
        ActivityLog::create($data);
    }
}