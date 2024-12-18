<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'domain'];
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($tenant) {
            if (!$tenant->slug) {
                $tenant->slug = \Str::slug($tenant->name);
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Project::class);
    }

    public function settings()
    {
        return $this->hasOne(TenantSetting::class);
    }

    // Method to check if the tenant has a specific domain or slug
    public static function findByDomainOrSlug($domain)
    {
        return static::where('domain', $domain)
            ->orWhere('slug', $domain)
            ->firstOrFail();
    }
}
