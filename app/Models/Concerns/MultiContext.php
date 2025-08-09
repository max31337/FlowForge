<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Hybrid multi-context ownership: a record belongs either to a tenant (tenant_id) OR
 * to an individual user (user_id) on the central domain. Never both.
 */
trait MultiContext
{
    protected static function bootMultiContext(): void
    {
        static::creating(function ($model) {
            // If tenancy is active, force tenant ownership
            if (function_exists('tenancy') && tenancy()->initialized) {
                if ($model->isFillable('tenant_id')) {
                    $model->tenant_id = tenant('id');
                }
                if ($model->isFillable('user_id')) {
                    $model->user_id = null; // ensure personal ownership not mixed
                }
            } elseif (auth()->check()) { // Personal mode
                if ($model->isFillable('user_id') && empty($model->user_id)) {
                    $model->user_id = auth()->id();
                }
                if ($model->isFillable('tenant_id')) {
                    $model->tenant_id = null;
                }
            }
        });
    }

    /**
     * Scope records to current execution context (tenant or personal user).
     */
    public function scopeContext(Builder $query): Builder
    {
        if (function_exists('tenancy') && tenancy()->initialized) {
            return $query->where($this->getTable().'.tenant_id', tenant('id'));
        }
        if (auth()->check()) {
            return $query->where($this->getTable().'.user_id', auth()->id());
        }
        return $query->whereRaw('1 = 0'); // block anonymous access
    }
}
