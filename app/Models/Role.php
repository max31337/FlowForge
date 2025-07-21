<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Role extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'tenant_id',
        'is_default',
        'is_system',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Get the tenant that owns the role.
     * Note: tenant_id can be null for central admin roles.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the users that have this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the permissions for the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
                    ->using(RolePermission::class)
                    ->withTimestamps();
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Grant permission to role.
     */
    public function givePermissionTo(string|Permission $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }

        if (!$this->hasPermission($permission->name)) {
            $this->permissions()->attach($permission);
        }
    }

    /**
     * Revoke permission from role.
     */
    public function revokePermissionTo(string|Permission $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }

        $this->permissions()->detach($permission);
    }

    /**
     * Sync permissions for role.
     */
    public function syncPermissions(array $permissions): void
    {
        $permissionIds = collect($permissions)->map(function ($permission) {
            if (is_string($permission)) {
                return Permission::where('name', $permission)->firstOrFail()->id;
            }
            return $permission instanceof Permission ? $permission->id : $permission;
        });

        $this->permissions()->sync($permissionIds);
    }

    /**
     * Scope to get roles for a specific tenant.
     */
    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope to get roles for current tenant.
     */
    public function scopeForCurrentTenant($query)
    {
        if (tenancy()->initialized) {
            return $query->where('tenant_id', tenant('id'));
        }
        
        return $query->whereNull('tenant_id'); // Central admin roles
    }

    /**
     * Scope to get default roles.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get system roles.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Check if this is a tenant owner role.
     */
    public function isOwner(): bool
    {
        return $this->name === 'owner';
    }

    /**
     * Check if this is an admin role.
     */
    public function isAdmin(): bool
    {
        return in_array($this->name, ['owner', 'admin']);
    }

    /**
     * Check if this is a central admin role.
     */
    public function isCentralAdmin(): bool
    {
        return is_null($this->tenant_id);
    }
}
