<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'invitation_code',
    ];

    /**
     * Relationships
     */

    // A tenant has many users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // A tenant has many invitation codes
    public function invitationCodes()
    {
        return $this->hasMany(InvitationCode::class);
    }

    // A tenant has one settings record
    public function settings()
    {
        return $this->hasOne(TenantSetting::class);
    }

    // A tenant has many projects
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // A tenant has many tasks (via polymorphism or directly)
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // A tenant has many reports
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // A tenant has one subscription
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }
}