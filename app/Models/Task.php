<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'user_id',
        'project_id',
        'tenant_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
}